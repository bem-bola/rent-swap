<?php

namespace App\DTO;


class SearchDevice {
    public function __construct(
        public ?string $title,
        public ?string $category,
        public ?string $location,
        public ?float $priceMin,
        public ?float $priceMax,
    ){

    }
}