<?php

namespace STORMS\webframe\Modules\ImageManipulation;

class UriConfig { // TODO better name

    private string $path;
    private string|null $method;
    private ?int $width;
    private ?int $height;
    private ?int $quality;

    private bool $with_original;
    private bool $shuffle;

    private ?string $dest_format;
    private ?string $source_format;

    public function __construct(
        ?string $path                    = null,
        string|null $method             = null,
        ?string $source_format          = null,
        ?int $width                     = null,
        ?int $height                    = null,
        ?int $quality                   = null,
        ?string $dest_format            = null,
        bool $with_original             = false,
        bool $shuffle                   = false
    ) {
        if($path)
            $this->path = $path;
        $this->method = $method;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->dest_format = $dest_format;
        $this->source_format = $source_format;
        $this->with_original = $with_original;
        $this->shuffle = $shuffle;
    }

    public function getPath() : ?string {
        return $this->path;
    }

    public function setPath(string $path) : self {
        $this->path = $path;
        return $this;
    }

    public function getMethod() : ?string {
        return $this->method;
    }

    public function setMethod(?string $method) : self {
        $this->method = $method;
        return $this;
    }

    public function getWidth() : ?int {
        return $this->width;
    }

    public function setWidth(?int $width) : self {
        $this->width = $width;
        return $this;
    }

    public function getHeight() : ?int {
        return $this->height;
    }

    public function setHeight(?int $height) : self {
        $this->height = $height;
        return $this;
    }

    public function getQuality() : ?int {
        return $this->quality;
    }

    public function setQuality(?int $quality) : self {
        $this->quality = $quality;
        return $this;
    }

    public function getWithOriginal() : bool {
        return $this->with_original;
    }

    public function setWithOriginal(bool $with_original) : self {
        $this->with_original = $with_original;
        return $this;
    }

    public function getShuffle() : bool {
        return $this->shuffle;
    }

    public function setShuffle(bool $shuffle) : self {
        $this->shuffle = $shuffle;
        return $this;
    }

    public function getDestFormat() : ?string {
        return $this->dest_format;
    }

    public function setDestFormat(?string $dest_format) : self {
        $this->dest_format = $dest_format;
        return $this;
    }

    public function getSourceFormat() : ?string {
        return $this->source_format;
    }

    public function setSourceFormat(?string $source_format) : self {
        $this->source_format = $source_format;
        return $this;
    }

}
