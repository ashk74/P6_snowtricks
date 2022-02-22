<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EmbedVideo extends AbstractExtension
{
    private $iframe;

    public function getFunctions()
    {
        return [
            new TwigFunction('embed_video', [$this, 'embedVideo']),
        ];
    }

    public function embedVideo(string $url, string $videoTitle): string
    {
        $parsedUrl = parse_url($url);

        if ($parsedUrl['host'] === 'youtu.be') {
            $this->iframe = '<iframe class="col-md-4 mb-3" width="560px" height="315px" src="https://www.youtube.com/embed' . $parsedUrl['path'] . '" title="' . $videoTitle . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        } else {
            // $this->iframe = '<div class="col-md-4" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"> <iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="https://www.dailymotion.com/embed/video' . $parsedUrl['path'] . '?autoplay=1" width="100%" height="100%" allowfullscreen allow="autoplay"></iframe></div>';
            $this->iframe = '<iframe class="col-md-4 mb-3" frameborder="0" width="560" height="315" src="https://www.dailymotion.com/embed/video' . $parsedUrl['path'] . '" title="' . $videoTitle . '"allowfullscreen allow="autoplay; fullscreen; picture-in-picture"></iframe>';
        }

        return $this->iframe;
    }
}
