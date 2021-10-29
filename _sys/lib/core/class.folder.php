<?php
class Folder {
    public static function Load($path, $recursive = false) {
        if (is_dir($path)):
            if ($dh = opendir($path)):
                while(($file = readdir($dh)) !== false):
                    if(!preg_match('/\.+/', $file) && filetype($path . $file) == 'dir' && $recursive):
                        Folder::Load($path . $file . "/", $recursive);
                    else:
                        if (preg_match('/\.php$/', $file)):
                            require_once($path . $file);
                        endif;
                    endif;
                endwhile;
                closedir($dh);
            endif;
        endif;
    }
}

?>
