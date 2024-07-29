<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $categories = [
            'Category1',
            'Category2',
            'Category3',
        ];
        
        $modelData = [];
        foreach ($categories as $category) {
            $modelData[$category] = [
                'available' => false,
                'option_1' => false,
                'option_2' => false,
            ];
        }

        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([

                        Tab::make('Control')
                            ->schema(
                                collect($modelData)
                                    ->mapWithKeys(fn (array $data, string $category) => [
                                        $category => Fieldset::make($category)
                                            ->columns(3)
                                            ->schema(
                                                (new Collection($data))
                                                    ->mapWithKeys(function ($value, $key) use ($category) {
                                                        return [
                                                            $key => Forms\Components\Checkbox::make("categories.{$category}.{$key}")
                                                                ->hidden(fn (Get $get): bool => $key !== 'available' && ! $get("categories.{$category}.available"))
                                                                ->default($value)
                                                                ->live(condition: in_array($key, ['available', 'option_2']))
                                                        ];
                                                    })
                                                    ->toArray()
                                            ),
                                    ])
                                    ->toArray()
                            ),

                        Tab::make('Hidden Dummy Tab 1')
                            ->hidden(fn (Get $get): bool => (new Collection($get('categories')))
                                    ->filter(fn (array $data) => $data['available'])
                                    ->filter(fn (array $data) => $data['option_2'])
                                    ->isEmpty()
                            )
                            ->schema([
                                Fieldset::make('Dummy fields of conditionally hidden tab')
                                    ->schema([
                                        Forms\Components\TextInput::make('www.up'),
                                    ]),
                            ]),

                        Tab::make('Empty Dummy Tab 3')
                            ->schema([
                                Fieldset::make('Hidden Dummy fields')
                                    ->hidden(fn (Get $get): bool => (new Collection($get('categories')))
                                        ->filter(fn (array $data) => $data['available'])
                                        ->filter(fn (array $data) => $data['option_2'])
                                        ->isEmpty()
                                    )
                                    ->schema([
                                        Forms\Components\TextInput::make('abc.def'),
                                    ]),
                            ]),

                        Tab::make('Dummy Tab 4')
                            ->schema([
                                Fieldset::make('Dummy fieldset from last tab')
                                    ->schema([
                                        Forms\Components\Checkbox::make('todo.check'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
        ];
    }
}
