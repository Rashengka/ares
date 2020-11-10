<?php
declare(strict_types = 1);

namespace App\Presenters\templates;

use App\Core\Utils\UI\Flash;
use Nette\Security\User;
use stdClass;

/**
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 * @property-read User $user
 * @property-read string $baseUrl
 * @property-read string $basePath
 * @property-read Flash[]|stdClass[] $flashes
 */
class LayoutParams
{
}
