<?php

declare(strict_types=1);

namespace Sushil\Makegui\DataTables\Adminarea;

use Illuminate\Database\Eloquent\Builder;
use Cortex\Foundation\DataTables\AbstractDataTable;
use Sushil\Makegui\Transformers\Adminarea\AdminTransformer;

use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;

class AdminsDataTable extends AbstractDataTable
{
    /**
     * {@inheritdoc}
     */
    protected $model = Admin::class;

    /**
     * Get Ajax form.
     *
     * @return string
     */
    protected function getAjaxForm(): string
    {
        return '#adminarea-admins-filters-form';
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        $link = config('cortex.foundation.route.locale_prefix')
            ? '"<a href=\""+routes.route(\'adminarea.admins.edit\', {admin: full.id, locale: \''.$this->request->segment(1).'\'})+"\">"+data+"</a>"'
            : '"<a href=\""+routes.route(\'adminarea.admins.edit\', {admin: full.id})+"\">"+data+"</a>"';

        return [];
    }

      /**
     * Get table id.
     *
     * @return string
     */
    protected function getTableId() : string
    {
        // here define table id must be same as define in controller id. check index() function of controller
        $tableId='adminarea-members-index-table';
        return $tableId;
    }

     /**
     * Get table Editor Fields.
     *
     * @return string
     */
    protected function getEditorFields()
    {
        // here define Fies of datatable
        return Editor::make()
            ->fields([
                Fields\Text::make('name'),
               Fields\Text::make('entity_type'),
            ]);
    }

}
