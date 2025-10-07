<?php

namespace ngatngay;

use ZipArchive;
use SplFileInfo;

class zip extends ZipArchive {
    public function add(string $path, ?string $relative = null): bool
    {
        if (!file_exists($path)) {
            return false;
        }
        
        $file = new SplFileInfo($path);
        $path = $file->getPathname();
        $pathRelative = $path;

        if ($relative) {
            $pathRelative = substr($path, strlen($relative));
        }
    
        if ($file->isFile()) {
            $this->addFile($path, $pathRelative);
        }
        
        if ($file->isDir()) {
            $this->addEmptyDir($pathRelative);
        }
        
        return true;
    }
}
