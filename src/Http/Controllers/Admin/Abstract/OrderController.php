<?php

namespace Netto\Http\Controllers\Admin\Abstract;

use Illuminate\Http\{RedirectResponse, Response};

use Netto\Http\Controllers\Admin\Abstract\CrudController as BaseController;
use Netto\Traits\Crud\AdminActions;

use App\Models\Order as WorkModel;
use App\Http\Requests\Admin\OrderRequest as WorkRequest;

use App\Models\{Merchandise, User};
use Netto\Models\{Delivery, OrderHistory};

abstract class OrderController extends BaseController
{
    use AdminActions;

    protected string $baseRoute = 'store.order';
    protected string $className = WorkModel::class;

    protected array $crudTitle = [
        'list' => 'main.list_order',
        'edit' => 'main.edit_order',
        'create' => 'main.create_order',
    ];

    protected string $itemRouteId = 'order';

    protected array $viewId = [
        'list' => 'cms::order.index',
        'edit' => 'cms::order.order',
    ];

    /**
     * @return Response
     */
    public function create(): Response
    {
        $model = $this->createModel();
        $model->loadCart();

        return $this->form($model, [
            'url' => [
                'index' => $this->getRouteIndex(),
                'save' => $this->getRouteStore(),
            ],
            'history' => [],
        ]);
    }

    /**
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        /** @var WorkModel $model */
        $model = $this->getModel($id);
        $model->loadCart();

        return $this->form($model, [
            'url' => [
                'index' => $this->getRouteIndex(),
                'save' => $this->getRoute('update', $model),
                'destroy' => $this->getRoute('destroy', $model),
            ],
            'history' => $this->getHistory($model),
        ]);
    }

    /**
     * @param WorkRequest $request
     * @return RedirectResponse
     */
    public function store(WorkRequest $request): RedirectResponse
    {
        $model = $this->createModel();
        return $this->redirect($model, $request);
    }

    /**
     * @param WorkRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(WorkRequest $request, string $id): RedirectResponse
    {
        $model = $this->getModel($id);
        return $this->redirect($model, $request);
    }

    /**
     * @return WorkModel
     */
    protected function createModel(): WorkModel
    {
        $return = new $this->className();
        $return->setAttribute('currency_id', get_default_currency_id());
        $return->setAttribute('status_id', get_default_order_status_id());

        return $return;
    }

    /**
     * @param WorkModel $object
     * @return array
     */
    protected function getHistory(WorkModel $object): array
    {
        $return = [];
        $defaultLanguageCode = get_default_language_code();

        foreach ($object->history->all() as $item) {
            /** @var OrderHistory $item */
            $return[] = [
                'name' => "[{$item->status->getAttribute('slug')}] {$item->status->getTranslated('name')[$defaultLanguageCode]}",
                'date' => format_date($item->getAttribute('created_at')),
                'user' => $item->user ? "[{$item->user->getAttribute('id')}] {$item->user->getAttribute('name')}" : '',
            ];
        }

        return $return;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getItem(array $item): array
    {
        if (array_key_exists('status.slug', $item)) {
            $item['status.slug'] = "[{$item['status.slug']}] ".get_order_status_list()[$item['status.slug']]['name'];
        }

        if (array_key_exists('currency.slug', $item)) {
            $item['currency.slug'] = get_currency_symbol($item['currency.slug']);
        }

        if (array_key_exists('weight', $item)) {
            $item['weight'] = format_number($item['weight']);
        }

        if (array_key_exists('volume', $item)) {
            $item['volume'] = format_number($item['volume']);
        }

        return $item;
    }

    /**
     * @return array
     */
    protected function getMerchandiseLabels(): array
    {
        $return = [];
        $defaultLanguageCode = get_default_language_code();

        foreach (Merchandise::query()->select(['id', 'slug'])->with(['translated'])->get() as $item) {
            /** @var Merchandise $item */
            $return[$item->getAttribute('id')] = "{$item->getTranslated('name')[$defaultLanguageCode]} [{$item->getAttribute('slug')}]";
        }

        return $return;
    }

    /**
     * @param $model
     * @return array
     */
    protected function getReference($model): array
    {
        return [
            'status' => get_labels_order_status(),
            'user' => get_labels(User::class, true),
            'delivery' => get_labels(Delivery::class, true),
            'currency' => get_labels_currency(),
            'merchandise' => $this->getMerchandiseLabels(),
        ];
    }
}
