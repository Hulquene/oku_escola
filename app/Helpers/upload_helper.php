<?php
// app/Helpers/upload_helper.php

if (!function_exists('upload_media')) {
    /**
     * Upload media file with validation and optimization
     * 
     * @param CodeIgniter\HTTP\Files\UploadedFile $file The uploaded file
     * @param string $folder Subfolder within uploads directory
     * @param array $options Upload options
     * @return array Result with success, file_name, file_path, error
     */
    function upload_media($file, $folder = 'general', $options = [])
    {
        // Default options
        $defaults = [
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
            'max_size' => 5120, // 5MB in KB
            'width' => null,
            'height' => null,
            'create_thumb' => false,
            'thumb_width' => 150,
            'thumb_height' => 150,
            'random_name' => true,
            'overwrite' => false,
            'min_width' => null,
            'min_height' => null,
            'is_favicon' => false,
            'quality' => 90 // For image compression
        ];
        
        $options = array_merge($defaults, $options);
        
        // Check if file is valid
        if (!$file || !$file->isValid()) {
            return [
                'success' => false,
                'error' => $file ? $file->getErrorString() : 'Nenhum ficheiro enviado'
            ];
        }
        
        // GUARDAR INFORMAÇÕES ANTES DE MOVER (CRÍTICO)
        $fileSize = $file->getSize();
        $fileMimeType = $file->getClientMimeType(); // ✅ Guardar ANTES
        $extension = strtolower($file->getExtension());
        $originalName = $file->getName();
        $tempPath = $file->getTempName();
        
        // Check file size (in KB)
        if ($fileSize / 1024 > $options['max_size']) {
            return [
                'success' => false,
                'error' => "Ficheiro demasiado grande. Tamanho máximo: " . ($options['max_size'] / 1024) . "MB"
            ];
        }
        
        // Check file type
        if (!in_array($extension, $options['allowed_types'])) {
            return [
                'success' => false,
                'error' => "Tipo de ficheiro não permitido. Tipos permitidos: " . implode(', ', $options['allowed_types'])
            ];
        }
        
        // Validate image dimensions if requested (USAR TEMP PATH)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && file_exists($tempPath)) {
            $imageInfo = getimagesize($tempPath);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                
                // Check minimum dimensions
                if ($options['min_width'] && $width < $options['min_width']) {
                    return [
                        'success' => false,
                        'error' => "Largura mínima: {$options['min_width']}px. A imagem tem {$width}px"
                    ];
                }
                
                if ($options['min_height'] && $height < $options['min_height']) {
                    return [
                        'success' => false,
                        'error' => "Altura mínima: {$options['min_height']}px. A imagem tem {$height}px"
                    ];
                }
                
                // Check exact dimensions (for favicon)
                if ($options['is_favicon']) {
                    $validFaviconSizes = [16, 32, 64, 128, 256];
                    if ($width != $height || !in_array($width, $validFaviconSizes)) {
                        return [
                            'success' => false,
                            'error' => 'Favicon deve ser quadrado: 16x16, 32x32, 64x64, 128x128 ou 256x256 pixels'
                        ];
                    }
                }
            }
        }
        
        // Generate file name
        $fileName = $options['random_name'] ? $file->getRandomName() : $file->getName();
        
        // Ensure folder exists
        $uploadPath = FCPATH . 'uploads/' . $folder;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Check if file exists
        if (!$options['overwrite'] && file_exists($uploadPath . '/' . $fileName)) {
            // Add timestamp to avoid duplicate
            $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;
        }
        
        // Move the file
        try {
            $file->move($uploadPath, $fileName);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao mover ficheiro: ' . $e->getMessage()
            ];
        }
        
        $filePath = $uploadPath . '/' . $fileName;
        $relativePath = 'uploads/' . $folder . '/' . $fileName;
        
        // Resize image if dimensions specified (and it's an image)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && 
            ($options['width'] || $options['height'])) {
            
            resize_image($filePath, $options['width'], $options['height'], $options['quality']);
        }
        
        // Create thumbnail if requested
        if ($options['create_thumb'] && in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $thumbName = pathinfo($fileName, PATHINFO_FILENAME) . '_thumb.' . $extension;
            $thumbPath = $uploadPath . '/' . $thumbName;
            
            if (copy($filePath, $thumbPath)) {
                resize_image($thumbPath, $options['thumb_width'], $options['thumb_height'], $options['quality']);
            }
        }
        
        return [
            'success' => true,
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'file_size' => $fileSize, // ✅ AGORA DEFINIDA
            'file_type' => $fileMimeType, // ✅ VALOR GUARDADO ANTES
            'extension' => $extension,
            'original_name' => $originalName
        ];
    }
}

if (!function_exists('resize_image')) {
    /**
     * Resize an image using GD library
     * 
     * @param string $filePath Full path to image
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @param int $quality JPEG quality (0-100)
     * @return bool Success
     */
    function resize_image($filePath, $maxWidth = null, $maxHeight = null, $quality = 90)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }
        
        list($width, $height, $type) = $imageInfo;
        
        // If no dimensions specified or image already smaller, skip
        if ((!$maxWidth && !$maxHeight) || 
            ($maxWidth && $width <= $maxWidth && $maxHeight && $height <= $maxHeight)) {
            return true;
        }
        
        // Calculate new dimensions
        $newWidth = $width;
        $newHeight = $height;
        
        if ($maxWidth && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        }
        
        if ($maxHeight && $newHeight > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = floor($width * ($maxHeight / $height));
        }
        
        // Create image resource based on type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $src = imagecreatefromwebp($filePath);
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }
        
        if (!$src) {
            return false;
        }
        
        // Create new image
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Preserve transparency for GIF
        if ($type == IMAGETYPE_GIF) {
            $transparentIndex = imagecolortransparent($src);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($src, $transparentIndex);
                $transparentIndex = imagecolorallocate($dst, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($dst, 0, 0, $transparentIndex);
                imagecolortransparent($dst, $transparentIndex);
            }
        }
        
        // Resize
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dst, $filePath, $quality);
                break;
            case IMAGETYPE_PNG:
                $pngQuality = 9 - floor(($quality / 100) * 9); // Convert to 0-9 scale (9 = lowest compression)
                imagepng($dst, $filePath, $pngQuality);
                break;
            case IMAGETYPE_GIF:
                imagegif($dst, $filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    imagewebp($dst, $filePath, $quality);
                }
                break;
        }
        
        // Clean up
        imagedestroy($src);
        imagedestroy($dst);
        
        return true;
    }
}

if (!function_exists('delete_media')) {
    /**
     * Delete media file
     * 
     * @param string $filePath Relative path from FCPATH
     * @return bool Success
     */
    function delete_media($filePath)
    {
        $fullPath = FCPATH . $filePath;
        
        // Also delete thumbnail if exists
        $thumbPath = preg_replace('/(\.[^.]+)$/', '_thumb$1', $fullPath);
        
        $deleted = false;
        
        if (file_exists($fullPath)) {
            $deleted = unlink($fullPath);
        }
        
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
        
        return $deleted;
    }
}

if (!function_exists('get_media_url')) {
    /**
     * Get URL for media file
     * 
     * @param string $filePath Relative path
     * @param string $default Default image if file doesn't exist
     * @return string URL
     */
    function get_media_url($filePath, $default = '')
    {
        if ($filePath && file_exists(FCPATH . $filePath)) {
            return base_url($filePath);
        }
        
        return $default ? base_url($default) : '';
    }
}

if (!function_exists('upload_base64_image')) {
    /**
     * Upload base64 encoded image
     * 
     * @param string $base64Data Base64 image data
     * @param string $folder Folder to save
     * @param array $options Upload options
     * @return array Result
     */
    function upload_base64_image($base64Data, $folder = 'general', $options = [])
    {
        // Extract mime type and data
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $imageType = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $base64Data = base64_decode($base64Data);
            
            if (!$base64Data) {
                return [
                    'success' => false,
                    'error' => 'Dados de imagem inválidos'
                ];
            }
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'upload_') . '.' . $imageType;
            file_put_contents($tempFile, $base64Data);
            
            // Create UploadedFile instance
            $file = new \CodeIgniter\Files\File($tempFile);
            
            // Use regular upload function
            $result = upload_media($file, $folder, $options);
            
            // Clean up temp file
            unlink($tempFile);
            
            return $result;
        }
        
        return [
            'success' => false,
            'error' => 'Formato de imagem base64 inválido'
        ];
    }
}