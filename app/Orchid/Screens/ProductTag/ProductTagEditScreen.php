<?php

namespace App\Orchid\Screens\ProductTag;

use Orchid\Screen\Screen;
use App\Models\ProductTag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Picture;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;

class ProductTagEditScreen extends Screen
{
    /**
     * @var ProductTag
     */
    public $tag;

    /**
     * Query data.
     */
    public function query(ProductTag $tag): iterable
    {
        return [
            'tag' => $tag
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return $this->tag->exists ? 'Редактировать тег: ' . $this->tag->title : 'Создать новый тег';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Управление информацией о теге продуктов';
    }

    /**
     * Button commands.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->tag->exists)
                ->confirm('Вы уверены, что хотите удалить этот тег?'),
        ];
    }

    /**
     * Views.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('tag.title')
                    ->title('Заголовок')
                    ->placeholder('Введите заголовок тега')
                    ->help('Основное название тега')
                    ->required(),

                Input::make('tag.alt_title')
                    ->title('Альтернативный заголовок')
                    ->placeholder('Введите альтернативный заголовок')
                    ->help('Используется для отображения в определенных местах'),

                Input::make('tag.slug')
                    ->title('Слаг (URL)')
                    ->placeholder('Автоматически генерируется из заголовка')
                    ->help('Уникальный идентификатор для URL. Оставьте пустым для автогенерации'),

                Input::make('tag.template')
                    ->title('Шаблон')
                    ->placeholder('Название шаблона для отображения')
                    ->help('Необязательно. Используется для кастомного отображения'),

                Picture::make('tag.image')->title('Изображение тега')->help('Загрузите изображение для тега (необязательно)')->storage('public')->targetRelativeUrl(),

                Quill::make('tag.description')
                    ->title('Описание')
                    ->placeholder('Подробное описание тега')
                    ->help('Полное описание тега для пользователей'),

                Input::make('tag.seo_title')
                    ->title('SEO заголовок')
                    ->placeholder('SEO заголовок для поисковых систем')
                    ->help('Заголовок для отображения в результатах поиска'),

                TextArea::make('tag.seo_description')
                    ->title('SEO описание')
                    ->rows(3)
                    ->placeholder('SEO описание для поисковых систем')
                    ->help('Описание для отображения в результатах поиска (до 160 символов)'),
            ])
        ];
    }

    /**
     * Save tag
     */
    public function save(Request $request, ProductTag $tag): void
    {
        $data = $request->validate([
            'tag.title' => 'required|string|max:255',
            'tag.alt_title' => 'required|string|max:255',
            'tag.slug' => 'nullable|string|max:255|unique:product_tags,slug,' . $tag->id,
            'tag.template' => 'nullable|string|max:255',
            'tag.image' => 'nullable|string|max:800',
            'tag.description' => 'nullable|string',
            'tag.seo_title' => 'nullable|string|max:255',
            'tag.seo_description' => 'nullable|string|max:500',
        ]);

        // Автогенерация слага если не указан
        if (empty($data['tag']['slug'])) {
            $data['tag']['slug'] = Str::slug($data['tag']['title']);
        }

        $tag->fill($data['tag'])->save();

        Alert::info('Тег сохранен успешно');
    }

    /**
     * Remove tag
     */
    public function remove(ProductTag $tag): void
    {
        $tag->delete();

        Alert::info('Тег удален успешно');
    }
}
