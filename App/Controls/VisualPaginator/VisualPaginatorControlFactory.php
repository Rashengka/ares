<?php
declare(strict_types = 1);

namespace App\Controls\VisualPaginator;

interface VisualPaginatorControlFactory
{
    public function create(): VisualPaginatorControl;
}
