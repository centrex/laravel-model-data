<?php

declare(strict_types = 1);

use Centrex\ModelData\Models\Data;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModelDataWidget extends Model
{
    protected $table = 'widgets';

    protected $guarded = [];
}

beforeEach(function (): void {
    Schema::create('widgets', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    (include __DIR__ . '/../database/migrations/2023_11_15_010000_create_model_data_table.php')->up();
});

it('writes and reads data through putForModel/getForModel', function (): void {
    $widget = ModelDataWidget::create(['name' => 'Widget A']);

    expect(Data::getForModel($widget))->toBe([]);

    Data::putForModel($widget, ['tracking_number' => 'CTRX-000001', 'carrier' => 'Connect Courier']);

    expect(Data::getForModel($widget))->toBe([
        'tracking_number' => 'CTRX-000001',
        'carrier'         => 'Connect Courier',
    ]);
});

it('upserts on repeated putForModel calls instead of creating duplicate rows', function (): void {
    $widget = ModelDataWidget::create(['name' => 'Widget B']);

    Data::putForModel($widget, ['status' => 'pending']);
    Data::putForModel($widget, ['status' => 'shipped']);

    expect(Data::query()->forModel($widget)->count())->toBe(1)
        ->and(Data::getForModel($widget))->toBe(['status' => 'shipped']);
});

it('scopes forModel to the correct model type and id, not just a matching id', function (): void {
    $widgetOne = ModelDataWidget::create(['name' => 'Widget C']);
    $widgetTwo = ModelDataWidget::create(['name' => 'Widget D']);

    Data::putForModel($widgetOne, ['owner' => 'one']);
    Data::putForModel($widgetTwo, ['owner' => 'two']);

    expect(Data::getForModel($widgetOne))->toBe(['owner' => 'one'])
        ->and(Data::getForModel($widgetTwo))->toBe(['owner' => 'two']);
});
