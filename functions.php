<?php
    require_once "MySQLiConfig.php";

    #Pure function
    function convertToGD($tempPath) {
        $allowedMimeTypes = [
            'image/jpeg',
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
                case 'image/jpeg':
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

    #Pure function
    function makePNG($sourceImage,$file) {
        imagealphablending($sourceImage, false);
        imagesavealpha($sourceImage, true);
        return imagepng($sourceImage,$file);
    }

    function findUsernameByID($id) {
        $func_db = MySQLiConfig::getInstance();
        $func_db->connect();

        $Query = "SELECT username FROM users WHERE id = '$id'";
        $rez = mysqli_fetch_array($func_db->Execute($Query));
        $func_db -> Disconnect();
        return $rez['username'];
    }

    function findHashtagByID($id) {
        $func_db = MySQLiConfig::getInstance();
        $func_db->connect();

        $Query = "SELECT hashtag_content FROM hashtags WHERE id = '$id'";
        $rez = mysqli_fetch_array($func_db->Execute($Query));
        $func_db -> Disconnect();
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

    #Pure function
    function checkUserConsumption(User $usr) {
        $tier = $usr->getUserTier();
        $uploads = $usr->getWeeklyPicturesUploaded();

        switch ($tier) {
            case 3: return true;
            case 1: if ($uploads>=7) return false; else return true;
            case 2: if ($uploads>=35) return false; else return true;
        }
    }

    function findIDByUsername($username){
        $db =MySQLiConfig::getInstance();
        $db->connect();
        $Q = "SELECT id from users where username='$username'";
        $rez = mysqli_fetch_row($db->Execute($Q));
        $db->Disconnect();
        if ($rez!=null) return $rez[0];
        else return null;
    }

    #Pure function
    function constructPhotoUploadQuery($esc_name, $user_id, $desc = null, $hash_array=null){
        $data_fields = ['hashtag_1','hashtag_2','hashtag_3','hashtag_4','hashtag_5'];

        $Q = "INSERT INTO photos (photo_location,user_id";
        if (sizeof($hash_array)) {
            for ($i = 0; $i < sizeof($hash_keys); $i++) {
                $Q .= ',' . $data_fields[$i];
            }
        }
        if (isset($desc)){
            $esc_description = $db->EscapeString($desc);
            $Q .= ',description';
        }

        $Q .= ") VALUES ('$esc_name',$user_id";
        if (sizeof($hash_array)) {
            for ($i = 0; $i < sizeof($hash_keys); $i++) {
                $Q .= ',' . $hash_keys[$i];
            }
        }
        if (isset($desc)) $Q .= ",'$esc_description'";
        $Q .= ")";
        return $Q;
    }

    #Pure function
    function constructPhotoEditQuery($desc=null,$hash_array=null,$id,DBConnectionInterface $db): ?string {
        $data_fields = ['hashtag_1','hashtag_2','hashtag_3','hashtag_4','hashtag_5'];

        if(isset($desc)) {
            $esc_description = $db->EscapeString($desc);
            $dsc_flag = true;
        } else $dsc_flag = false;

        if (sizeof($hash_array)) {
            $hash_flag = true;
        } else $hash_flag = false;

        if ($hash_flag || $dsc_flag) {
            $query = "UPDATE photos SET ";

            if ($dsc_flag) {
                $query .='description = "'.$esc_description.' " ';
            }
            if ($hash_flag) {
                if($dsc_flag) $query .=", ";
                for ($j = 0; $j < sizeof($hash_keys); $j++) {
                    $query.=$data_fields[$j].' = '.$hash_keys[$j].' ';
                }
            }
            $query .= " WHERE id = '$id'";
            return $query;
        }
        else return null;
    }
