<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use Illuminate\Contracts\View\View;

class ClinicController extends Controller
{
    public function home(): View
    {
        return $this->page('home', __('site.seo.home_title'), __('site.seo.home_description'));
    }

    public function about(): View
    {
        return $this->page('about-modern', __('site.seo.about_title'), __('site.seo.about_description'));
    }

    public function services(): View
    {
        return $this->page('services', __('site.seo.services_title'), __('site.seo.services_description'));
    }

    public function appointment(): View
    {
        return $this->page('appointment', __('site.seo.appointment_title'), __('site.seo.appointment_description'));
    }

    public function contact(): View
    {
        return $this->page('contact-modern', __('site.seo.contact_title'), __('site.seo.contact_description'));
    }

    public function legal(): View
    {
        return $this->page('legal', 'Mentions légales', 'Informations légales du cabinet dentaire.');
    }

    public function privacy(): View
    {
        return $this->page('privacy', 'Politique de confidentialité', 'Informations sur la protection de vos données.');
    }

    private function page(string $view, string $title, string $description): View
    {
        $clinic = ClinicSetting::apply(config('clinic'));
        $phoneHref = preg_replace('/[^0-9+]/', '', $clinic['phone']);
        $whatsappBase = $clinic['whatsapp_number'] ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $clinic['whatsapp_number']) : 'https://wa.me/';

        return view('pages.'.$view, [
            'clinic' => $clinic,
            'rtl' => app()->getLocale() === 'ar',
            'phoneHref' => $phoneHref,
            'whatsappUrl' => $whatsappBase.'?text='.rawurlencode(__('site.whatsapp_message')),
            'seo' => [
                'title' => $title,
                'description' => $description,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
