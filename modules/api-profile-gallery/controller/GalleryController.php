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

class GalleryController extends \Api\Controller
{
	public function indexAction() {
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        list($page, $rpp) = $this->req->getPager();

        $profile_name = $this->req->param->name;
        $profile = Profile::getOne(['id'=>$profile_name]);
        if(!$profile)
            $profile = Profile::getOne(['name'=>$profile_name]);
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

    public function singleAction() {
        if(!$this->app->isAuthorized())
            return $this->resp(401);

        $profile_name = $this->req->param->name;
        $profile = Profile::getOne(['id'=>$profile_name]);
        if(!$profile)
            $profile = Profile::getOne(['name'=>$profile_name]);
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
}