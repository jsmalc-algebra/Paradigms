<?php
    require_once "ConnectToDatabase.php";
    function convertToGD($tempPath) {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp'
        ];

        $imageInfo = getimagesize($tempPath);
        if ($imageInfo === false) return false;
        elseif (!in_array($imageInfo['mime'], $allowedMimeTypes)) return false;
        else {
            switch ($imageInfo['mime']) {
                case 'image/jpeg' or 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($tempPath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($tempPath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($tempPath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($tempPath);
                    break;
                case 'image/bmp':
                    $sourceImage = imagecreatefrombmp($tempPath);
                    break;
            }
            return $sourceImage;
        }
    }

    function makePNG($sourceImage,$file) {
        imagealphablending($sourceImage, false);
        imagesavealpha($sourceImage, true);
        return imagepng($sourceImage,$file);
    }

    function findUsernameByID($id) {
        $func_db = ConnectToDatabase::getInstance();
        $func_db->connect();

        $Query = "SELECT username FROM users WHERE id = '$id'";
        $rez = mysqli_fetch_array($func_db->Execute($Query));
        $func_db -> close();
        return $rez['username'];
    }

    function findHashtagByID($id) {
        $func_db = ConnectToDatabase::getInstance();
        $func_db->connect();

        $Query = "SELECT hashtag_content FROM hashtags WHERE id = '$id'";
        $rez = mysqli_fetch_array($func_db->Execute($Query));
        $func_db -> close();
        return $rez['hashtag_content'];
    }

    function generateImageShowcaseFromArray($array)
    {
        echo "<div class='col img_showcase'>";

        echo "<a href='image_showcase.php?id=".$array[0]."' class='img_link link-light'>";

        echo "<img src=images/photos/".$array[1]." height='200' width='200'>";

        echo "<p>Author:  ".findUsernameByID($array[4])." </p>";

        if (isset($array[3])) {
            $desc = $array[3];
            echo "<p>Description : " . $desc . "</p>";
        }

        $time = $array[2];
        echo"<p>Upload timestamp: $time</p>";

        for($i=0;$i<5;$i++) {
            if($array[$i+5] == null) continue;
            $hashtags[]=$array[$i+5];
        }
        if (!empty($hashtags)) {
            echo "<p>Hashtags:";
            foreach ($hashtags as $hashtag) {
                echo " ".findHashtagByID($hashtag);
            }
            echo "</p>";
        }
        echo "</a>";
        echo "</div>";
    }
    function checkUserConsumption(User $usr) {
        $tier = $usr->getUserTier();
        $uploads = $usr->getWeeklyPicturesUploaded();

        switch ($tier) {
            case 3: return true;
            case 1: if ($uploads>=7) return false; else return true;
            case 2: if ($uploads>=35) return false; else return true;
        }
    }

    function findIDByUsername($username)
    {
        $db =ConnectToDatabase::getInstance();
        $db->connect();
        $Q = "SELECT id from users where username='$username'";
        $rez = mysqli_fetch_row($db->Execute($Q));
        $db->close();
        if ($rez!=null) return $rez[0];
        else return null;
    }
