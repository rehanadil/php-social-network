<?php

namespace SocialKit;

class registerMedia
{
    private $conn;
    private $siteConfig;
    private $escapeObj;
    private $folder;
    private $timelineId;

    private $photoExtensions = array('jpg', 'jpeg', 'gif', 'png');
    private $videoExtensions = array('mp4', 'mpeg', 'mov');
    private $audioExtensions = array('mp3', 'ogg', 'wav');
    private $documentExtensions = array('pdf', 'txt', 'zip', 'rar', 'tar', 'doc', 'docx');
    private $photoMimes = array('image/jpeg', 'image/gif', 'image/png');
    private $minimumBytes = 1;
    private $maximumBytes = 1;
    
    private $albumId = 0;
    private $albumName = '';
    private $albumDescription = '';

    private $filenum = 0;
    private $file;
    private $id;
    private $path;
    private $pathname;
    private $name;
    private $extension;
    private $bytes;

    private $imageWidth;
    private $imageHeight;
    private $imageMime;

    function __construct()
    {
        global $conn, $config;
        $this->conn = $conn;
        $this->siteConfig = $config;
        $this->escapeObj = new \SocialKit\Escape();
        $this->maximumBytes = 10 * MB;

        if ($this->siteConfig['photo_filesize_limit'] == 0) $this->siteConfig['photo_filesize_limit'] = 50;
        if ($this->siteConfig['video_filesize_limit'] == 0) $this->siteConfig['video_filesize_limit'] = 50;
        if ($this->siteConfig['audio_filesize_limit'] == 0) $this->siteConfig['audio_filesize_limit'] = 50;
        if ($this->siteConfig['document_filesize_limit'] == 0) $this->siteConfig['document_filesize_limit'] = 50;
        return $this;
    }

    public function setConnection(\mysqli $conn)
    {
        $this->conn = $conn;
        return $this;
    }

    private function getConnection()
    {
        return $this->conn;
    }

    public function register()
    {
        if (isset($this->file))
        {
            $info = array();

            foreach ($this->file as $i => $Unused)
            {
                $this->filenum = $i;
                $this->readFileInfo();

                if (!isset($this->timelineId)) $this->setTimeline();

                if ($this->size > $this->minimumBytes)
                {
                    if (in_array($this->extension, $this->photoExtensions))
                    {
                        $this->maximumBytes = $this->siteConfig['photo_filesize_limit'] * MB;
                        if ($this->size < $this->maximumBytes) $info[] = $this->registerImage();
                    }
                    elseif (in_array($this->extension, $this->videoExtensions))
                    {
                        $this->maximumBytes = $this->siteConfig['video_filesize_limit'] * MB;
                        if ($this->size < $this->maximumBytes) $info[] = $this->registerVideo();
                    }
                    elseif (in_array($this->extension, $this->audioExtensions))
                    {
                        $this->maximumBytes = $this->siteConfig['audio_filesize_limit'] * MB;
                        if ($this->size < $this->maximumBytes) $info[] = $this->registerAudio();
                    }
                    elseif (in_array($this->extension, $this->documentExtensions))
                    {
                        $this->maximumBytes = $this->siteConfig['document_filesize_limit'] * MB;
                        if ($this->size < $this->maximumBytes) $info[] = $this->registerDocument();
                    }
                }
            }

            return $info;
        }
    }

    private function registerImage()
    {
        $this->readImageInfo();
        $this->setFolder('photos');
        $this->createFolder();

        $createSQL = $this->getConnection()->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('" . $this->extension . "','" . $this->name . "','photo')");

        if ($createSQL)
        {
            $this->id = $this->getConnection()->insert_id;
            $this->pathname = $this->folder . '/' . md5($this->id);
            $this->path = $this->pathname . '.' . $this->extension;

            if (move_uploaded_file($this->file[$this->filenum]['tmp_name'], $this->path))
            {
                $this->imageOrient();
                $length = ($this->imageWidth > $this->imageHeight) ? $this->imageHeight : $this->imageWidth;
                $length = ($length > 920) ? 920 : $length;
                
                $processes = array(
                    'thumbnail' => array(
                        'type' => 'crop',
                        'width' => 64,
                        'height' => 64,
                        'name' => $this->pathname . '_thumb'
                    ),
                    'square' => array(
                        'type' => 'crop',
                        'width' => $length,
                        'height' => $length,
                        'name' => $this->pathname . '_100x100'
                    ),
                    'wide' => array(
                        'type' => 'crop',
                        'width' => $length,
                        'height' => floor($length * 0.75),
                        'name' => $this->pathname . '_100x75'
                    ),
                    'original' => array(
                        'type' => 'resize',
                        'width' => ($this->imageWidth > 920) ? 920 : $this->imageWidth,
                        'height' => 0,
                        'name' => $this->pathname
                    )
                );

                foreach ($processes as $processImage)
                {
                    $processPath = $processImage['name'] . '.' . $this->extension;
                    $this->process($processImage['type'], $processPath, $processImage['width'], $processImage['height']);
                }

                $this->getConnection()->query("UPDATE " . DB_MEDIA . " SET timeline_id=" . $this->timelineId . ",album_id=" . $this->albumId . ",url='" . $this->pathname . "',temp=0,active=1 WHERE id=" . $this->id);

                return array(
                    'id' => $this->id,
                    'active' => 1,
                    'extension' => $this->extension,
                    'name' => $this->name,
                    'url' => $this->pathname
                );
            }
        }

        chmod($this->folder, 0755);
    }

    private function registerVideo()
    {
        $this->setFolder('videos');
        $this->createFolder();

        $createSQL = $this->getConnection()->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('" . $this->extension . "','" . $this->name . "','video')");

        if ($createSQL)
        {
            $this->id = $this->getConnection()->insert_id;
            $this->pathname = $this->folder . '/' . md5($this->id);
            $this->path = $this->pathname . '.' . $this->extension;

            if (move_uploaded_file($this->file[$this->filenum]['tmp_name'], $this->path))
            {
                $this->getConnection()->query("UPDATE " . DB_MEDIA . " SET timeline_id=" . $this->timelineId . ",album_id=" . $this->albumId . ",url='" . $this->pathname . "',temp=0,active=1 WHERE id=" . $this->id);

                return array(
                    'id' => $this->id,
                    'active' => 1,
                    'extension' => $this->extension,
                    'name' => $this->name,
                    'url' => $this->pathname
                );
            }
        }

        chmod($this->folder, 0755);
    }

    private function registerAudio()
    {
        $this->setFolder('audios');
        $this->createFolder();

        $createSQL = $this->getConnection()->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('" . $this->extension . "','" . $this->name . "','audio')");

        if ($createSQL)
        {
            $this->id = $this->getConnection()->insert_id;
            $this->pathname = $this->folder . '/' . md5($this->id);
            $this->path = $this->pathname . '.' . $this->extension;

            if (move_uploaded_file($this->file[$this->filenum]['tmp_name'], $this->path))
            {
                $this->getConnection()->query("UPDATE " . DB_MEDIA . " SET timeline_id=" . $this->timelineId . ",album_id=" . $this->albumId . ",url='" . $this->pathname . "',temp=0,active=1 WHERE id=" . $this->id);

                return array(
                    'id' => $this->id,
                    'active' => 1,
                    'extension' => $this->extension,
                    'name' => $this->name,
                    'url' => $this->pathname
                );
            }
        }

        chmod($this->folder, 0755);
    }

    private function registerDocument()
    {
        $this->setFolder('documents');
        $this->createFolder();

        $createSQL = $this->getConnection()->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('" . $this->extension . "','" . $this->name . "','document')");

        if ($createSQL)
        {
            $this->id = $this->getConnection()->insert_id;
            $this->pathname = $this->folder . '/' . md5($this->id);
            $this->path = $this->pathname . '.' . $this->extension;

            if (move_uploaded_file($this->file[$this->filenum]['tmp_name'], $this->path))
            {
                $this->getConnection()->query("UPDATE " . DB_MEDIA . " SET timeline_id=" . $this->timelineId . ",album_id=" . $this->albumId . ",url='" . $this->pathname . "',temp=0,active=1 WHERE id=" . $this->id);

                return array(
                    'id' => $this->id,
                    'active' => 1,
                    'extension' => $this->extension,
                    'name' => $this->name,
                    'url' => $this->pathname
                );
            }
        }

        chmod($this->folder, 0755);
    }

    public function setFile($f)
    {
        if (is_uploaded_file($f['tmp_name'][0]))
        {
            $this->file = $f;
            $this->sortFiles();
        }
    }

    public function setName($n)
    {
        $this->name = $this->escapeObj->stringEscape($n);
        return $this->name;
    }

    public function setMaximumBiyes($b)
    {
        $this->maximumBytes = (int) $b;
    }

    private function setFolder($d)
    {
        $this->folder = 'uploads/' . $d . '/' . date('Y') . '/' . date('m');

    }

    public function setPath($p)
    {
        if (file_exists($p)) $this->path = $p;
    }

    public function setTimeline($t=0)
    {
        if (!$t)
        {
            $userId = $GLOBALS['user']['id'];
            $this->timelineId = $userId;
            return true;
        }

        $this->timelineId = (int) $t;
    }

    private function createFolder()
    {
        if (! file_exists($this->folder))
        {
            mkdir($this->folder, 0777, true);
        }
        else
        {
            chmod($this->folder, 0755);
        }
    }

    private function readFileInfo()
    {
        $this->name = $this->readName();
        $this->extension = $this->readExtension();
        $this->size = $this->readSize();
    }

    private function readName()
    {
        return $this->setName($this->file[$this->filenum]['name']);
    }

    private function readExtension()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    private function readSize()
    {
        return $this->file[$this->filenum]['size'];
    }

    private function readImageInfo()
    {
        $im = getimagesize($this->file[$this->filenum]['tmp_name']);
        $this->imageWidth = floor($im[0]);
        $this->imageHeight = floor($im[1]);
        $this->imageMime = $im['mime'];
    }

    private function imageOrient()
    {
        if (! in_array($this->extension, array('jpeg', 'jpg')))
        {
            return false;
        }

        $im = imagecreatefromjpeg($this->path);
        $exif = @exif_read_data($this->path);
     
        if (! empty($exif['Orientation']))
        {
            switch ($exif['Orientation'])
            {
                case 3:
                    $im = imagerotate($im, 180, 0);
                    break;

                case 6:
                    $im = imagerotate($im, -90, 0);
                    break;
                    
                case 8:
                    $im = imagerotate($im, 90, 0);
                    break;
            }
        }

        imagejpeg($im, $this->path);
        return true;
    }

    public function process($task, $savePath, $taskWidth=0, $taskHeight=0, $quality=80)
    {
        $quality = (int) $quality;
        $quality = ($quality < 0 or $quality > 100) ? 80 : $quality;

        if (file_exists ($this->path))
        {
            if (in_array($this->extension, $this->photoExtensions))
            {
                if ($this->extension == "gif")
                {
                    copy($this->path, $savePath);
                    return true;
                }

                switch ($this->extension)
                {
                    case 'png':
                        $im = imagecreatefrompng($this->path);
                        break;
                    
                    default:
                        $im = imagecreatefromjpeg($this->path);
                        break;
                }

                if ($task == "crop")
                {
                    if ($taskWidth > 0 && $taskHeight > 0)
                    {
                        $cropWidth = $this->imageWidth;
                        $cropHeight = $this->imageHeight;
                        $ratioWidth = 1;
                        $ratioHeight = 1;
                        $destinationX = 0;
                        $destinationY = 0;
                        $sourceX = 0;
                        $sourceY = 0;

                        $cropWidth = ($taskWidth == 0 || $taskWidth > $this->imageWidth) ? $this->imageWidth : $taskWidth;
                        $cropHeight = ($taskHeight == 0 || $taskHeight > $this->imageHeight) ? $this->imageHeight : $taskHeight;
                        
                        if ($cropWidth > $this->imageWidth)
                        {
                            $destinationX = ($cropWidth - $this->imageWidth) / 2;
                        }
                        
                        if ($cropHeight > $this->imageHeight)
                        {
                            $destinationY = ($cropHeight - $this->imageHeight) / 2;
                        }
                        
                        if ($cropWidth < $this->imageWidth || $cropHeight < $this->imageHeight)
                        {
                            $ratioWidth = $cropWidth / $this->imageWidth;
                            $ratioHeight = $cropHeight / $this->imageHeight;
                            
                            if ($cropHeight > $this->imageHeight)
                            {
                                $sourceX  = ($this->imageWidth - $cropWidth) / 2;
                            }
                            elseif ($cropWidth > $this->imageWidth)
                            {
                                $sourceY  = ($this->imageHeight - $cropHeight) / 2;
                            }
                            else
                            {
                                if ($ratioHeight > $ratioWidth)
                                {
                                    $sourceX = round(($this->imageWidth - ($cropWidth / $ratioHeight)) / 2);
                                }
                                else
                                {
                                    $sourceY = round(($this->imageHeight - ($cropHeight / $ratioWidth)) / 2);
                                }
                            }
                        }
                        
                        $cropImage = @imagecreatetruecolor($cropWidth, $cropHeight);
                        
                        if ($this->extension == "png")
                        {
                            @imagesavealpha($cropImage, true);
                            @imagefill($cropImage, 0, 0, @imagecolorallocatealpha($cropImage, 0, 0, 0, 127));
                        }
                        
                        @imagecopyresampled($cropImage, $im ,$destinationX, $destinationY, $sourceX, $sourceY, $cropWidth - 2 * $destinationX, $cropHeight - 2 * $destinationY, $this->imageWidth - 2 * $sourceX, $this->imageHeight - 2 * $sourceY);
                        
                        @imageinterlace($cropImage, true);

                        switch ($this->extension)
                        {
                            case 'png':
                                @imagepng($cropImage, $savePath);
                                break;
                            
                            default:
                                @imagejpeg($cropImage, $savePath, $quality);
                                break;
                        }

                        @imagedestroy($cropImage);
                    }
                }
                elseif ($task == "resize")
                {
                    if ($taskWidth == 0 && $taskHeight == 0)
                    {
                        return false;
                    }
                    
                    if ($taskWidth > 0 && $taskHeight == 0)
                    {
                        $resizeWidth = $taskWidth;
                        $resizeRatio = $resizeWidth / $this->imageWidth;
                        $resizeHeight = floor($this->imageHeight * $resizeRatio);
                    }
                    elseif ($taskHeight > 0 && $taskWidth == 0)
                    {
                        $resizeHeight = $taskHeight;
                        $resizeRatio = $resizeHeight / $this->imageHeight;
                        $resizeWidth = floor($this->imageWidth * $resizeRatio);
                    }
                    elseif ($taskWidth > 0 && $taskHeight > 0)
                    {
                        $resizeWidth = $taskWidth;
                        $resizeHeight = $taskHeight;
                    }
                    
                    $resizeImage = @imagecreatetruecolor($resizeWidth, $resizeHeight);
                    
                    if ($this->extension == "png")
                    {
                        @imagesavealpha($resizeImage, true);
                        @imagefill($resizeImage, 0, 0, @imagecolorallocatealpha($resizeImage, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($resizeImage, $im, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $this->imageWidth, $this->imageHeight);
                    @imageinterlace($resizeImage, true);

                    switch ($this->extension)
                    {
                        case 'png':
                            @imagepng($resizeImage, $savePath);
                            break;
                        
                        default:
                            @imagejpeg($resizeImage, $savePath, $quality);
                            break;
                    }

                    @imagedestroy($resizeImage);
                }
                elseif ($task == "scale")
                {
                    if ($taskWidth == 0)
                    {
                        $taskWidth = 100;
                    }
                    
                    if ($taskHeight == 0)
                    {
                        $taskHeight = 100;
                    }
                    
                    $scaleWidth = $this->imageWidth * ($taskWidth / 100);
                    $scaleHeight = $this->imageHeight * ($taskHeight / 100);
                    $scaleImage = @imagecreatetruecolor($scaleWidth, $scaleHeight);
                    
                    if ($this->extension == "png")
                    {
                        @imagesavealpha($scaleImage, true);
                        @imagefill($scaleImage, 0, 0, imagecolorallocatealpha($scaleImage, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($scaleImage, $im, 0, 0, 0, 0, $scaleWidth, $scaleHeight, $this->imageWidth, $this->imageHeight);
                    @imageinterlace($scaleImage, true);

                    switch ($this->extension)
                    {
                        case 'png':
                            @imagepng($scaleImage, $savePath);
                            break;
                        
                        default:
                            @imagejpeg($scaleImage, $savePath, $quality);
                            break;
                    }

                    @imagedestroy($scaleImage);
                }
            }
        }
    }

    private function sortFiles()
    {
        $sort = array();

        if (is_array($this->file['name']))
        {
            foreach ($this->file['name'] as $key => $value)
            {
                $sort[$key] = array(
                    'name' => $this->file['name'][$key],
                    'type' => $this->file['type'][$key],
                    'tmp_name' => $this->file['tmp_name'][$key],
                    'error' => $this->file['error'][$key],
                    'size' => $this->file['size'][$key]
                );
            }
        }
        else
        {
            $sort[0] = array(
                'name' => $this->file['name'],
                'type' => $this->file['type'],
                'tmp_name' => $this->file['tmp_name'],
                'error' => $this->file['error'],
                'size' => $this->file['size']
            );
        }

        $this->file = $sort;
        return true;
    }

    public function createAlbum()
    {
        if (!isset($this->timelineId)) $this->setTimeline();
        $auto = (empty($this->albumName)) ? 1 : 0;
        $sql = $this->getConnection()->query("INSERT INTO " . DB_MEDIA . "
            (timeline_id,active,name,descr,type,temp)
            VALUES
            (" . $this->timelineId . ",1,'" . $this->albumName . "','" . $this->albumDescription . "','album',$auto)");
        if ($sql)
        {
            $this->albumId = $this->getConnection()->insert_id;
            return $this->albumId;
        }
    }

    public function setAlbumName($n)
    {
        $this->albumName = $this->escapeObj->stringEscape($n);
    }

    public function setAlbumDescription($d)
    {
        $this->albumDescription = $this->escapeObj->postEscape($d);
    }
}