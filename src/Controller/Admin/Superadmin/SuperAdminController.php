<?php

namespace App\Controller\Admin\Superadmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Interfaces\UploaderInterface;

use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Entity\Category;


/**
 * @Route("/admin/su")
 */
class SuperAdminController extends AbstractController
{

    /**
     * @Route("/upload-video-locally", name="upload_video_locally")
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader)
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $base_path = Video::uploadFolder;
            $video->setPath($base_path.$fileName[0]);
            $video->setTitle($fileName[1]);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }


        return $this->render('admin/upload_video_locally.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/upload-video-by-vimeo", name="upload_video_by_vimeo")
     */
    public function uploadVideoByVimeo(Request $request)
    {
        $vimeo_id = preg_replace('/^\/.+\//','',$request->get('video_uri'));
        if($request->get('videoName') && $vimeo_id)
        {
            $em = $this->getDoctrine()->getManager();
            $video = new Video();
            $video->setTitle($request->get('videoName'));
            $video->setPath(Video::VimeoPath.$vimeo_id);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }
        return $this->render('admin/upload_video_vimeo.html.twig');
    }


    /**
     * @Route("/set-video-duration/{video}/{vimeo_id}", name="set_video_duration", requirements={"vimeo_id"=".+"})
     */
    public function setVideoDuration(Video $video, $vimeo_id)
    {
        if( !is_numeric($vimeo_id) )
        {
            // you can handle here setting duration for locally stored files
            // ....
            return $this->redirectToRoute('videos');
        }

        $user_vimeo_token = $this->getUser()->getVimeoApiKey();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vimeo.com/videos/{$vimeo_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/vnd.vimeo.*+json;version=3.4",
                "Authorization: Bearer $user_vimeo_token",
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: '.$err);
        }
        else
        {
            $duration =  json_decode($response, true)['duration'] / 60;

            if($duration)
            {
                $video->setDuration($duration);
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
            }
            else
            {
                $this->addFlash(
                    'danger',
                    'We were not able to update duration. Check the video.'
                );
            }

            return $this->redirectToRoute('videos');
        }

    }


    /**
     * @Route("/delete-video/{video}/{path}", name="delete_video", requirements={"path"=".+"})
     */
    public function deleteVideo(Video $video, $path, UploaderInterface $fileUploader)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        if( $fileUploader->delete($path) )
        {
            $this->addFlash(
                'success',
                'The video was successfully deleted.'
            );
        }
        else
        {
            $this->addFlash(
                'danger',
                'We were not able to delete. Check the video.'
            );
        }

        return $this->redirectToRoute('videos');

    }

    /**
     * @Route("/update-video-category/{video}", methods={"POST"}, name="update_video_category")
     */
    public function updateVideoCategory(Request $request, Video $video)
    {

        $em = $this->getDoctrine()->getManager();

        $category = $this->getDoctrine()->getRepository(Category::class)->find($request->request->get('video_category'));

        $video->setCategory($category);

        $em->persist($video);
        $em->flush();

        return $this->redirectToRoute('videos');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy([], ['name' => 'ASC']);
        return $this->render('admin/users.html.twig',['users'=>$users]);
    }


    /**
     * @Route("/delete-user/{user}", name="delete_user")
     */
    public function deleteUser(User $user)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('users');
    }


}
