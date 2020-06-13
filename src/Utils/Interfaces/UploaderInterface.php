<?php

namespace App\Utils\Interfaces;

interface UploaderInterface {

    public function upload($file);
    public function delete($path);

}
