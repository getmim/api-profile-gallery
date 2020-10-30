<?php
/**
 * GalleryController
 * @package api-profile-gallery
 * @version 0.0.1
 */

namespace ApiProfileGallery\Controller;

use LibFormatter\Library\Formatter;
use Profile\Model\Profile;
use ProfileGallery\Model\ProfileGallery as PGallery;
use LibForm\Library\Form;

class GalleryController extends \Api\Controller
{
    private function getProfile(string $name): ?object{
        $profile_name = $this->req->param->name;
        $profile = Profile::getOne(['id'=>$profile_name]);
        if($profile)
            return $profile;
        return Profile::getOne(['name'=>$profile_name]);
    }

    public function createAction(){
        if(!$this->profile->isLogin())
            return $this->resp(401);

        $profile = $this->getProfile($this->req->param->name);
        if(!$profile)
            return $this->show404();

        if($profile->id != $this->profile->id)
            return $this->show404();

        $form = new Form('api.profile-gallery.create');

        if(!($valid = $form->validate()))
            return $this->resp(422, $form->getErrors());

        $valid->profile = $profile->id;
        $valid->images  = json_encode($valid->images);

        $id = PGallery::create((array)$valid);

        $gallery = PGallery::getOne(['id'=>$id]);

        $gallery = Formatter::format('profile-gallery', $gallery, ['profile']);

        $this->resp(0, $gallery);
    }

	public function indexAction() {
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        list($page, $rpp) = $this->req->getPager();

        $profile = $this->getProfile($this->req->param->name);
        if(!$profile)
        	return $this->show404();

        $cond = [
            'profile' => $profile->id
        ];
        if($q = $this->req->getQuery('q'))
            $cond['q'] = $q;

        $pages = PGallery::get($cond, $rpp, $page, ['created' => false]);
        $pages = !$pages ? [] : Formatter::formatMany('profile-gallery', $pages, ['profile']);
        foreach($pages as &$pg)
            $pg->profile = (object)['id'=>$pg->profile->id];
        unset($pg);

        $this->resp(0, $pages, null, [
            'meta' => [
                'page'  => $page,
                'rpp'   => $rpp,
                'total' => PGallery::count($cond)
            ]
        ]);
    }

    public function removeAction(){
        if(!$this->profile->isLogin())
            return $this->resp(401);

        $profile = $this->getProfile($this->req->param->name);
        if(!$profile)
            return $this->show404();

        if($profile->id != $this->profile->id)
            return $this->show404();

        $cond = [
            'id'      => $this->req->param->id,
            'profile' => $profile->id
        ];

        $gallery = PGallery::getOne($cond);
        if(!$gallery)
            return $this->show404();

        PGallery::remove(['id'=>$gallery->id]);

        $this->resp(0, 'success');
    }

    public function singleAction() {
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        $profile = $this->getProfile($this->req->param->name);
        if(!$profile)
            return $this->show404();

        $cond = [
        	'id'      => $this->req->param->id,
            'profile' => $profile->id
        ];

        $gallery = PGallery::getOne($cond);
        if(!$gallery)
        	return $this->show404();

        $gallery = Formatter::format('profile-gallery', $gallery, ['profile']);

        $this->resp(0, $gallery);
    }

    public function updateAction(){
        if(!$this->profile->isLogin())
            return $this->resp(401);

        $profile = $this->getProfile($this->req->param->name);
        if(!$profile)
            return $this->show404();

        if($profile->id != $this->profile->id)
            return $this->show404();

        $cond = [
            'id'      => $this->req->param->id,
            'profile' => $profile->id
        ];

        $gallery = PGallery::getOne($cond);
        if(!$gallery)
            return $this->show404();

        $form = new Form('api.profile-gallery.edit');

        if(!($valid = $form->validate($gallery)))
            return $this->resp(422, $form->getErrors());

        $valid = (array)$valid;
        if($valid){
            if(isset($valid['images']))
                $valid['images'] = json_encode($valid['images']);

            PGallery::set((array)$valid, ['id'=>$gallery->id]);
            $gallery = PGallery::getOne(['id'=>$gallery->id]);
        }
        
        $gallery = Formatter::format('profile-gallery', $gallery, ['profile']);

        $this->resp(0, $gallery);
    }
}