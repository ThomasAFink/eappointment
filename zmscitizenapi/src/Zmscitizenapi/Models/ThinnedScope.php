<?php

namespace BO\Zmscitizenapi\Models;

use BO\Zmsentities\Provider;
use BO\Zmsentities\Schema\Entity;

class ThinnedScope extends Entity
{
    public static $schema = 'zmscitizenapi/schema/citizenapi/thinnedScope.json';

    /** @var int */
    public int $id;

    /** @var Provider|null */
    public ?Provider $provider;

    /** @var string|null */
    public ?string $shortName;

    /** @var bool|null */
    public ?bool $telephoneActivated;

    /** @var bool|null */
    public ?bool $telephoneRequired;

    /** @var bool|null */
    public ?bool $customTextfieldActivated;

    /** @var bool|null */
    public ?bool $customTextfieldRequired;

    /** @var string|null */
    public ?string $customTextfieldLabel;

    /** @var bool|null */
    public ?bool $captchaActivatedRequired;

    /** @var string|null */
    public ?string $displayInfo;

    public function __construct(
        int $id = 0,
        ?Provider $provider = null,
        ?string $shortName = null,
        ?bool $telephoneActivated = null,
        ?bool $telephoneRequired = null,
        ?bool $customTextfieldActivated = null,
        ?bool $customTextfieldRequired = null,
        ?string $customTextfieldLabel = null,
        ?bool $captchaActivatedRequired = null,
        ?string $displayInfo = null
    ) {
        $this->id = $id;
        $this->provider = $provider;
        $this->shortName = $shortName;
        $this->telephoneActivated = $telephoneActivated;
        $this->telephoneRequired = $telephoneRequired;
        $this->customTextfieldActivated = $customTextfieldActivated;
        $this->customTextfieldRequired = $customTextfieldRequired;
        $this->customTextfieldLabel = $customTextfieldLabel;
        $this->captchaActivatedRequired = $captchaActivatedRequired;
        $this->displayInfo = $displayInfo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'shortName' => $this->shortName,
            'telephoneActivated' => $this->telephoneActivated,
            'telephoneRequired' => $this->telephoneRequired,
            'customTextfieldActivated' => $this->customTextfieldActivated,
            'customTextfieldRequired' => $this->customTextfieldRequired,
            'customTextfieldLabel' => $this->customTextfieldLabel,
            'captchaActivatedRequired' => $this->captchaActivatedRequired,
            'displayInfo' => $this->displayInfo,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}