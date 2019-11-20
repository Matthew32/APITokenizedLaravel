<?php

namespace App\Repositories;

use App\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Mixed_;

class UserRepository implements UserRepositoryInterface
{

    /** save User
     * @param string $email
     * @param string $username
     * @param string $password
     * @return User
     */
    public function save(string $email, string $username, string $password)
    {
        $result = null;

        try {
            if (!empty($email) && !empty($username) && !empty($password))
                $result = User::create([
                    'email' => $email,
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        //dd([$email,$username,$password]);
        return $result;

    }


    public function update(int $id, array $contentToUpdate)
    {
        $result = null;

        try {
            $user = User::Find($id);
            foreach ($contentToUpdate as $key => $value)
                $user->$key = $value;

            $result = $user->save();
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error($e->getMessage());
        }
        //dd([$email,$username,$password]);
        return $result;
    }

    public function delete(int $id): bool
    {
        $result = false;
        try {
            $result = User::destroy($id) > 0;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        //dd([$email,$username,$password]);
        return $result;
    }

    public function createPicture(int $id)
    {
        $directory = "pic2" . DIRECTORY_SEPARATOR . $id . ".jpg";
        if(!is_dir("pic2")){
            mkdir("pic2");
        }
        $width = 125;
        $height = 250;
        // Create a200 x200 canvas image

        $canvas = imagecreatetruecolor($width, $height);


        // Allocate color for rectangle
        $colors = [
            0 => imagecolorallocate($canvas, 20, 105, 180),
            1 => imagecolorallocate($canvas, 255, 20, 180),
            3 => imagecolorallocate($canvas, 30, 20, 180)];


        // Draw rectangle with its color

        $y = 0;
        for ($j = 0; $j < ($height / 2); $j++) {
            for ($i = 0; $i < $width; $i++) {
                $x = $i == 0 ? 0 : $i + 50;

                imagefilledrectangle($canvas, $x, $y, 50, 50, imagecolorallocate($canvas, $i * $id, $i * $id, $i / $id));
            }
            $y = $y + 50;
        }

        imagejpeg($canvas, $directory, 75);

        imagedestroy($canvas);
        $fliped = imagecreatefromjpeg($directory);
        imageflip($fliped, IMG_FLIP_HORIZONTAL);

        $image = imagecreatefromjpeg($directory);

        $ImageTotal = imagecreatetruecolor(250, 250);


        imagecopy($ImageTotal, $image, 0, 0, 0, 0, 125, 250);

        imagecopy($ImageTotal, $fliped, 125, 0, 0, 0, 125, 250);
        imagejpeg($ImageTotal, $directory, 75);

        return $directory;
    }
}
