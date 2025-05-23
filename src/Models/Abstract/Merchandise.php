<?php

namespace Netto\Models\Abstract;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Netto\Models\Abstract\Model as BaseModel;
use Netto\Models\{Album, Cost, Price};
use Netto\Exceptions\NettoException;
use Netto\Traits\{HasUploads, IsMultiLingual};
use App\Models\{Section, MerchandiseLang};

/**
 * @property Album $album
 * @property Collection $costs
 * @property Collection $sections
 */

abstract class Merchandise extends BaseModel
{
    use HasUploads, IsMultiLingual;

    public array $multiLingual = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
    ];

    public string $multiLingualClass = MerchandiseLang::class;

    public array $costsData = [];

    public array $uploads = [
        'photo' => [
            'storage' => 'public',
            'width' => 900,
            'height' => 900,
        ],
        'thumb' => [
            'storage' => 'public',
            'width' => 150,
            'height' => 150,
            'auto' => 'photo',
        ],
    ];

    protected $table = 'cms_store__merchandise';

    protected $attributes = [
        'sort' => 0,
        'is_active' => '0',
        'width' => 0,
        'length' => 0,
        'height' => 0,
        'weight' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::saving(function(Merchandise $model): void {
            $model->prepareCosts();
        });

        self::saved(function(Merchandise $model): void{
            $model->saveCosts();
        });
    }

    /**
     * @return BelongsTo
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * @return BelongsToMany
     */
    public function costs(): BelongsToMany
    {
        return $this->belongsToMany(Price::class, Cost::class)->withPivot('currency_id', 'value')->using(Cost::class);
    }

    /**
     * @return BelongsToMany
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'cms_store__sections__merchandise', 'merchandise_id', 'section_id');
    }

    /**
     * @return void
     */
    public function loadCosts(): void
    {
        $defaultId = get_default_currency_id();

        foreach (get_price_list() as $code => $price) {
            $this->costsData[$code] = [
                'name' => $price['name'],
                'currency_id' => $defaultId,
                'value' => null,
            ];
        }

        if ($this->exists) {
            foreach ($this->costs->all() as $item) {
                $this->costsData[$item->slug]['currency_id'] = $item->pivot->currency_id;
                $this->costsData[$item->slug]['value'] = $item->pivot->value;
            }
        }
    }

    /**
     * Return the best price available to user, 0 if no price found.
     * <pre>
     * [$cost, $priceCode, $currencyCode, $formatted] = $object->getCost();
     * </pre>
     *
     * @return array
     * @throws NettoException
     */
    public function getCost(): array
    {
        if (!$this->exists) {
            return [];
        }

        $prices = get_price_list(true);
        $priceCode = null;

        $defaultCurrencyCode = get_default_currency_code();

        $cost = 0.00;
        $currencyCode = $defaultCurrencyCode;

        $costs = [];
        foreach ($this->costs->all() as $item) {
            /** @var Price $item */
            if (!array_key_exists($item->getAttribute('slug'), $prices)) {
                continue;
            }

            if ($item->pivot->getAttribute('currency_id') == $defaultCurrencyCode) {
                $costValue = $item->pivot->value;
            } else {
                $costValue = convert_currency(
                    $item->pivot->getAttribute('value'),
                    find_currency_code($item->pivot->getAttribute('currency_id'))
                );
            }

            $costs[$item->getAttribute('id')] = $costValue;
        }

        if ($costs) {
            asort($costs);

            /** @var Price $item */
            $item = $this->costs->find(key($costs));

            $cost = $item->pivot->getAttribute('value');
            $priceCode = $item->getAttribute('slug');
            $currencyCode = find_currency_code($item->pivot->getAttribute('currency_id'));
        }


        return [$cost, $priceCode, $currencyCode, format_currency($cost, $currencyCode)];
    }

    /**
     * @return void
     */
    protected function prepareCosts(): void
    {
        foreach (get_price_list() as $code => $price) {
            $keyValue = "costs|{$code}|value";
            $keyCurrencyId = "costs|{$code}|currency_id";

            $value = $this->getAttribute($keyValue);
            $currencyId = $this->getAttribute($keyCurrencyId);

            if (!is_null($value) && !is_null($currencyId)) {
                $this->costsData[$code]['value'] = (float) $value;
                $this->costsData[$code]['currency_id'] = (int) $currencyId;
            }

            unset($this->{$keyValue}, $this->{$keyCurrencyId});
        }
    }

    /**
     * @return void
     * @throws NettoException
     */
    protected function saveCosts(): void
    {
        $models = [];
        foreach ($this->costs->all() as $item) {
            $models[$item->slug] = $item->pivot;
        }

        foreach (get_price_list() as $code => $price) {
            if (!array_key_exists($code, $models)) {
                $model = new Cost();
                $model->setAttribute('price_id', $price['id']);
                $model->setAttribute('merchandise_id', $this->getAttribute('id'));

                $models[$code] = $model;
            }
        }

        foreach ($this->costsData as $code => $data) {
            foreach ($data as $key => $value) {
                $models[$code]->setAttribute($key, $value);
            }

            if (!$models[$code]->save()) {
                throw new NettoException(session('status', __('main.error_saving_model')));
            }
        }
    }
}
