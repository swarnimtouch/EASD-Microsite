<?php

namespace App\Support;

use App\Models\Webinar;

class CertificatePdf
{
    public static function delegateCertificate(
        string $templatePath,
        string $delegateName,
        ?float $nameX = null,
        ?float $nameY = null,
        ?int $fontSize = null,
        ?string $fontPath = null,
        ?string $fontColor = null
    ): string
    {
        if (self::isImageTemplate($templatePath)) {
            return self::generateCertificateFromImage(
                $templatePath,
                $delegateName,
                $nameX,
                $nameY,
                $fontSize,
                $fontPath,
                $fontColor
            );
        }

        $pdf = file_get_contents($templatePath);
        $delegateName = self::cleanText($delegateName);
        $fontPath = $fontPath && is_file($fontPath) && self::isEmbeddableTrueType($fontPath) ? $fontPath : null;

        $objectMap = self::objects($pdf);
        $maxObjectId = max(array_keys($objectMap));
        $fontFileObjectId = $fontPath ? $maxObjectId + 1 : null;
        $fontDescriptorObjectId = $fontPath ? $maxObjectId + 2 : null;
        $fontObjectId = $fontPath ? $maxObjectId + 3 : $maxObjectId + 1;
        $contentObjectId = $fontObjectId + 1;

        $pageObjectId = self::firstPageObjectId($objectMap);
        if (!$pageObjectId) {
            throw new \RuntimeException('Certificate template page was not found.');
        }

        $pageObject = $objectMap[$pageObjectId];
        $pageObject = self::addContentReference($pageObject, $contentObjectId);
        $pageObject = self::addFontResource($pageObject, $fontObjectId);

        $fontSize = $fontSize ?: self::fontSize($delegateName);
        $textWidth = strlen($delegateName) * $fontSize * 0.56;
        [$pageWidth] = self::pageSize($pageObject);
        $x = $nameX !== null ? max(10.0, $nameX - ($textWidth / 2.0)) : max(20.0, ($pageWidth - $textWidth) / 2.0);
        $y = $nameY ?? 292;

        [$r, $g, $b] = self::hexToRgb($fontColor);
        $colorCmd = sprintf("%.3f %.3f %.3f rg", $r / 255, $g / 255, $b / 255);

        $stream = implode("\n", [
            'BT',
            '/F99 ' . $fontSize . ' Tf',
            $colorCmd,
            '1 0 0 1 ' . self::num($x) . ' ' . self::num($y) . ' Tm',
            '(' . self::escapePdfString($delegateName) . ') Tj',
            'ET',
        ]);

        $append = "\n";
        $offsets = [];

        if ($fontPath && $fontFileObjectId && $fontDescriptorObjectId) {
            $fontContents = file_get_contents($fontPath);
            $compressedFont = gzcompress($fontContents);
            if ($fontContents === false || $compressedFont === false) {
                $fontPath = null;
            }
        }

        if ($fontPath && $fontFileObjectId && $fontDescriptorObjectId) {
            $offsets[$fontFileObjectId] = strlen($pdf . $append);
            $append .= $fontFileObjectId . " 0 obj\n<</Length " . strlen($compressedFont) . "/Length1 " . strlen($fontContents) . "/Filter/FlateDecode/Subtype/OpenType>>\nstream\n" . $compressedFont . "\nendstream\nendobj\n";

            $offsets[$fontDescriptorObjectId] = strlen($pdf . $append);
            $append .= $fontDescriptorObjectId . " 0 obj\n<</Type/FontDescriptor/FontName/AbrilDisplayItalic/Flags 64/FontBBox[-300 -300 1200 1000]/ItalicAngle -12/Ascent 900/Descent -250/CapHeight 700/StemV 80/FontFile3 {$fontFileObjectId} 0 R>>\nendobj\n";

            $offsets[$fontObjectId] = strlen($pdf . $append);
            $append .= $fontObjectId . " 0 obj\n<</Type/Font/Subtype/TrueType/BaseFont/AbrilDisplayItalic/Encoding/WinAnsiEncoding/FirstChar 32/LastChar 126/Widths[" . self::fontWidths() . "]/FontDescriptor {$fontDescriptorObjectId} 0 R>>\nendobj\n";
        } else {
            $offsets[$fontObjectId] = strlen($pdf . $append);
            $append .= $fontObjectId . " 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Times-Italic/Encoding/WinAnsiEncoding>>\nendobj\n";
        }

        $offsetContent = strlen($pdf . $append);
        $append .= $contentObjectId . " 0 obj\n<</Length " . strlen($stream) . ">>\nstream\n" . $stream . "\nendstream\nendobj\n";

        $offsetPage = strlen($pdf . $append);
        $append .= $pageObjectId . " 0 obj\n" . trim($pageObject) . "\nendobj\n";

        $xrefOffset = strlen($pdf . $append);
        $previousXref = self::previousXrefOffset($pdf);
        $rootObjectId = self::catalogObjectId($objectMap);
        $infoObjectId = self::infoObjectId($pdf);
        $size = $contentObjectId + 1;

        $append .= "xref\n";
        $append .= $pageObjectId . " 1\n" . self::xrefLine($offsetPage);
        if ($fontPath && $fontFileObjectId) {
            $append .= $fontFileObjectId . " 4\n";
            for ($objectId = $fontFileObjectId; $objectId <= $contentObjectId; $objectId++) {
                $append .= self::xrefLine($objectId === $contentObjectId ? $offsetContent : $offsets[$objectId]);
            }
        } else {
            $append .= $fontObjectId . " 2\n" . self::xrefLine($offsets[$fontObjectId]) . self::xrefLine($offsetContent);
        }
        $append .= "trailer\n<</Size {$size}/Root {$rootObjectId} 0 R";
        if ($infoObjectId) {
            $append .= "/Info {$infoObjectId} 0 R";
        }
        if ($previousXref > 0) {
            $append .= "/Prev {$previousXref}";
        }
        $append .= ">>\n";
        $append .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf . $append;
    }

    private static function objects(string $pdf): array
    {
        preg_match_all('/(\d+)\s+0\s+obj\s*(.*?)endobj/s', $pdf, $matches, PREG_SET_ORDER);
        $objects = [];

        foreach ($matches as $match) {
            $objects[(int) $match[1]] = $match[2];
        }

        if (!$objects) {
            throw new \RuntimeException('Certificate template has no readable PDF objects.');
        }

        return $objects;
    }

    private static function catalogObjectId(array $objects): int
    {
        foreach ($objects as $id => $object) {
            if (preg_match('/\/Type\s*\/Catalog\b/', $object)) {
                return (int) $id;
            }
        }

        throw new \RuntimeException('Certificate template catalog was not found.');
    }

    private static function firstPageObjectId(array $objects): ?int
    {
        foreach ($objects as $id => $object) {
            if (preg_match('/\/Type\s*\/Page\b/', $object)) {
                return (int) $id;
            }
        }

        return null;
    }

    private static function addContentReference(string $pageObject, int $contentObjectId): string
    {
        if (preg_match('/\/Contents\s*\[([^\]]*)]/s', $pageObject)) {
            return preg_replace(
                '/\/Contents\s*\[([^\]]*)]/s',
                '/Contents[$1 ' . $contentObjectId . ' 0 R]',
                $pageObject,
                1
            );
        }

        if (preg_match('/\/Contents\s+(\d+\s+\d+\s+R)/', $pageObject)) {
            return preg_replace(
                '/\/Contents\s+(\d+\s+\d+\s+R)/',
                '/Contents[$1 ' . $contentObjectId . ' 0 R]',
                $pageObject,
                1
            );
        }

        return self::appendToDictionary($pageObject, '/Contents ' . $contentObjectId . ' 0 R');
    }

    private static function addFontResource(string $pageObject, int $fontObjectId): string
    {
        $range = self::dictionaryRangeAfter($pageObject, '/Resources');
        if (!$range) {
            return self::appendToDictionary($pageObject, '/Resources<</Font<</F99 ' . $fontObjectId . ' 0 R>>>>');
        }

        [$start, $end] = $range;
        $resources = substr($pageObject, $start, $end - $start);
        $fontRange = self::dictionaryRangeAfter($resources, '/Font');

        if ($fontRange) {
            [$fontStart, $fontEnd] = $fontRange;
            $fontDictionary = substr($resources, $fontStart, $fontEnd - $fontStart);
            $fontDictionary = self::appendToDictionary($fontDictionary, '/F99 ' . $fontObjectId . ' 0 R');
            $resources = substr_replace($resources, $fontDictionary, $fontStart, $fontEnd - $fontStart);
        } else {
            $resources = self::appendToDictionary($resources, '/Font<</F99 ' . $fontObjectId . ' 0 R>>');
        }

        return substr_replace($pageObject, $resources, $start, $end - $start);
    }

    private static function dictionaryRangeAfter(string $text, string $keyword): ?array
    {
        $keywordPos = strpos($text, $keyword);
        if ($keywordPos === false) {
            return null;
        }

        $start = strpos($text, '<<', $keywordPos);
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $length = strlen($text);
        for ($i = $start; $i < $length - 1; $i++) {
            $pair = $text[$i] . $text[$i + 1];
            if ($pair === '<<') {
                $depth++;
                $i++;
                continue;
            }

            if ($pair === '>>') {
                $depth--;
                $i++;
                if ($depth === 0) {
                    return [$start, $i + 1];
                }
            }
        }

        return null;
    }

    private static function appendToDictionary(string $dictionary, string $entry): string
    {
        $lastClose = strrpos($dictionary, '>>');
        if ($lastClose === false) {
            throw new \RuntimeException('Certificate template contains an invalid PDF dictionary.');
        }

        return substr($dictionary, 0, $lastClose) . $entry . substr($dictionary, $lastClose);
    }

    private static function pageSize(string $pageObject): array
    {
        if (preg_match('/\/MediaBox\s*\[\s*[-\d.]+\s+[-\d.]+\s+([-\d.]+)\s+([-\d.]+)/', $pageObject, $match)) {
            return [(float) $match[1], (float) $match[2]];
        }

        return [841.89, 595.276];
    }

    private static function cleanText(string $text): string
    {
        $text = trim($text);
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return preg_replace('/[^\x20-\x7E]/', '', $converted ?: $text) ?: 'Delegate';
    }

    private static function isEmbeddableTrueType(string $fontPath): bool
    {
        $handle = fopen($fontPath, 'rb');
        if (!$handle) {
            return false;
        }

        $signature = fread($handle, 4);
        fclose($handle);

        return $signature === "\x00\x01\x00\x00" || $signature === 'true';
    }

    private static function fontSize(string $text): int
    {
        return match (true) {
            strlen($text) > 36 => 22,
            strlen($text) > 28 => 25,
            default => 30,
        };
    }

    private static function fontWidths(): string
    {
        return implode(' ', array_fill(0, 95, 560));
    }

    private static function escapePdfString(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function num(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private static function xrefLine(int $offset): string
    {
        return sprintf("%010d 00000 n \n", $offset);
    }

    private static function previousXrefOffset(string $pdf): int
    {
        if (preg_match_all('/startxref\s+(\d+)/', $pdf, $matches) && $matches[1]) {
            return (int) end($matches[1]);
        }

        return 0;
    }

    private static function infoObjectId(string $pdf): ?int
    {
        if (preg_match('/\/Info\s+(\d+)\s+0\s+R/', $pdf, $match)) {
            return (int) $match[1];
        }

        return null;
    }

    public static function generateSessionPostReadPdf(Webinar $webinar): string
    {
        $title = self::cleanText($webinar->title);
        $date = $webinar->scheduled_at ? $webinar->scheduled_at->format('d F Y') : 'Completed Session';
        $speciality = $webinar->speciality ? self::cleanText($webinar->speciality->name) : 'Cardiology';
        $faculty = $webinar->speakers->pluck('name')->implode(', ');
        if (!$faculty) {
            $faculty = $webinar->people->pluck('name')->implode(', ');
        }
        $faculty = $faculty ? self::cleanText($faculty) : 'PULCE Scientific Faculty';

        $stream = implode("\n", [
            'q',
            '0.031 0.231 0.561 rg',
            '0 760 595 82 re f',
            '0.745 0.118 0.176 rg',
            '0 754 595 6 re f',
            'BT',
            '/F1 18 Tf',
            '1 1 1 rg',
            '40 805 Td',
            '(PULCE Connect 2026 | Cardio-Renal-Metabolic Educational Series) Tj',
            '/F1 10 Tf',
            '0 -18 Td',
            '(European Society of Cardiology - Southeast Asia Scientific Session) Tj',
            'ET',
            'BT',
            '/F1 16 Tf',
            '0.031 0.231 0.561 rg',
            '40 705 Td',
            '(POST-READ SCIENTIFIC & CLINICAL SUMMARY) Tj',
            '/F1 10 Tf',
            '0.4 0.4 0.4 rg',
            '0 -16 Td',
            '(Official Presentation Handout & Post-Session Educational Resource) Tj',
            'ET',
            '0.85 0.85 0.85 RG',
            '1 w',
            '40 665 m 555 665 l S',
            '0.96 0.98 1.0 rg',
            '40 560 515 85 re f',
            '0.75 0.85 0.95 RG',
            '40 560 515 85 re S',
            'BT',
            '/F1 12 Tf',
            '0.1 0.1 0.1 rg',
            '55 622 Td',
            '(' . self::escapePdfString('Session: ' . substr($title, 0, 70)) . ') Tj',
            '/F1 10 Tf',
            '0.3 0.3 0.3 rg',
            '0 -18 Td',
            '(' . self::escapePdfString('Date: ' . $date . '   |   Speciality: ' . $speciality) . ') Tj',
            '0 -16 Td',
            '(' . self::escapePdfString('Distinguished Faculty: ' . substr($faculty, 0, 80)) . ') Tj',
            'ET',
            'BT',
            '/F1 13 Tf',
            '0.745 0.118 0.176 rg',
            '40 515 Td',
            '(KEY CLINICAL TAKEAWAYS & EVIDENCE HIGHLIGHTS) Tj',
            '/F1 10 Tf',
            '0.2 0.2 0.2 rg',
            '0 -24 Td',
            '(1. Comprehensive diagnostic guidelines and therapeutic management protocols for Heart Failure.) Tj',
            '0 -20 Td',
            '(2. Evidence-based pharmacotherapy: SGLT2 inhibitors, ARNI, and beta-blocker optimization.) Tj',
            '0 -20 Td',
            '(3. Patient stratification and real-world clinical case discussions presented by the faculty.) Tj',
            '0 -20 Td',
            '(4. Best practices for reducing hospital readmissions and improving cardiovascular outcomes.) Tj',
            'ET',
            '0.98 0.98 0.98 rg',
            '40 320 515 65 re f',
            '0.88 0.88 0.88 RG',
            '40 320 515 65 re S',
            'BT',
            '/F1 10 Tf',
            '0.2 0.2 0.2 rg',
            '55 362 Td',
            '(Educational Resource Access & Verification:) Tj',
            '0.4 0.4 0.4 rg',
            '0 -15 Td',
            '(This post-read educational summary was officially published for delegates of PULCE Connect 2026.) Tj',
            '0 -14 Td',
            '(For session recording snippets and interactive clinical discussions, visit your delegate dashboard.) Tj',
            'ET',
            '0.92 0.95 0.99 rg',
            '0 0 595 50 re f',
            'BT',
            '/F1 9 Tf',
            '0.4 0.4 0.4 rg',
            '40 22 Td',
            '(European Society of Cardiology - Technical support by AlphaMed | An education initiative by Hetero) Tj',
            'ET',
            'Q',
        ]);

        $streamLength = strlen($stream);

        $objects = [
            1 => "<</Type/Catalog/Pages 2 0 R>>",
            2 => "<</Type/Pages/Kids[3 0 R]/Count 1>>",
            3 => "<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>",
            4 => "<</Length {$streamLength}>>\nstream\n{$stream}\nendstream",
            5 => "<</Type/Font/Subtype/Type1/BaseFont/Helvetica/Encoding/WinAnsiEncoding>>",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$content}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<</Size 6/Root 1 0 R>>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    public static function isImageTemplate(string $path): bool
    {
        if (!is_file($path)) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true)) {
            return true;
        }

        $mime = @mime_content_type($path);
        return is_string($mime) && str_starts_with($mime, 'image/');
    }

    public static function generateCertificateFromImage(
        string $templatePath,
        string $delegateName,
        ?float $nameX = null,
        ?float $nameY = null,
        ?int $fontSize = null,
        ?string $fontPath = null,
        ?string $fontColor = null
    ): string
    {
        $cleanName = self::cleanText($delegateName);
        $ext = strtolower(pathinfo($templatePath, PATHINFO_EXTENSION));

        $imgInfo = @getimagesize($templatePath);
        $pixelW = $imgInfo[0] ?? 1920;
        $pixelH = $imgInfo[1] ?? 1080;
        $mime = $imgInfo['mime'] ?? 'image/jpeg';

        if ($mime === 'image/jpeg' || in_array($ext, ['jpg', 'jpeg'], true)) {
            $jpegData = file_get_contents($templatePath);
        } else {
            $srcImg = @imagecreatefromstring(file_get_contents($templatePath));
            if (!$srcImg) {
                throw new \RuntimeException('Unable to process certificate template image.');
            }
            $pixelW = imagesx($srcImg);
            $pixelH = imagesy($srcImg);
            ob_start();
            imagejpeg($srcImg, null, 95);
            $jpegData = ob_get_clean();
            imagedestroy($srcImg);
        }

        $pageWidth = 842.0;
        $pageHeight = 595.0;
        if ($pixelW > 0 && $pixelH > 0) {
            $ratio = $pixelW / $pixelH;
            if ($pixelW >= $pixelH) {
                $pageWidth = 842.0;
                $pageHeight = round($pageWidth / $ratio, 2);
            } else {
                $pageHeight = 842.0;
                $pageWidth = round($pageHeight * $ratio, 2);
            }
        }

        $fontSize = $fontSize ?: self::fontSize($cleanName);
        $textWidth = strlen($cleanName) * $fontSize * 0.56;
        $x = $nameX !== null ? max(10.0, $nameX - ($textWidth / 2.0)) : max(20.0, ($pageWidth - $textWidth) / 2.0);
        $y = $nameY !== null ? $nameY : ($pageHeight * 0.49);

        [$r, $g, $b] = self::hexToRgb($fontColor);
        $colorCmd = sprintf("%.3f %.3f %.3f rg", $r / 255, $g / 255, $b / 255);

        $stream = implode("\n", [
            'q',
            sprintf("%.2f 0 0 %.2f 0 0 cm", $pageWidth, $pageHeight),
            '/Im1 Do',
            'Q',
            'BT',
            '/F1 ' . $fontSize . ' Tf',
            $colorCmd,
            '1 0 0 1 ' . self::num($x) . ' ' . self::num($y) . ' Tm',
            '(' . self::escapePdfString($cleanName) . ') Tj',
            'ET',
        ]);

        $streamLength = strlen($stream);
        $imgLen = strlen($jpegData);

        $objects = [
            1 => "<</Type/Catalog/Pages 2 0 R>>",
            2 => "<</Type/Pages/Kids[3 0 R]/Count 1>>",
            3 => "<</Type/Page/Parent 2 0 R/MediaBox[0 0 {$pageWidth} {$pageHeight}]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>/XObject<</Im1 6 0 R>>>>>>",
            4 => "<</Length {$streamLength}>>\nstream\n{$stream}\nendstream",
            5 => "<</Type/Font/Subtype/Type1/BaseFont/Times-BoldItalic/Encoding/WinAnsiEncoding>>",
            6 => "<</Type/XObject/Subtype/Image/Width {$pixelW}/Height {$pixelH}/ColorSpace/DeviceRGB/BitsPerComponent 8/Filter/DCTDecode/Length {$imgLen}>>\nstream\n{$jpegData}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$content}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $totalObjects = count($objects) + 1;
        $pdf .= "xref\n0 {$totalObjects}\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<</Size {$totalObjects}/Root 1 0 R>>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    public static function hexToRgb(?string $hex): array
    {
        $hex = ltrim($hex ?? '#8e5f16', '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            return [142, 95, 22]; // Default gold/brown
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
