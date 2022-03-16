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

    /**
     * Format an iframe for Youtube or Dailymotion embed video
     *
     * @param string $url
     * @param string $videoTitle
     *
     * @return string
     */
    public function embedVideo(string $url, string $videoTitle): string
    {
        $parsedUrl = parse_url($url);

        if ($parsedUrl['host'] === 'youtu.be') {
            $this->iframe = '<iframe class="col-12" width="560px" height="315px" src="https://www.youtube.com/embed' . $parsedUrl['path'] . '" title="' . $videoTitle . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        } else {
            $this->iframe = '<iframe class="col-12" frameborder="0" width="560" height="315" src="https://www.dailymotion.com/embed/video' . $parsedUrl['path'] . '" title="' . $videoTitle . '"allowfullscreen allow="autoplay; fullscreen; picture-in-picture"></iframe>';
        }

        return $this->iframe;
    }
}
