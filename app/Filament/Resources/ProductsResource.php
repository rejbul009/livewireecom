<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class ProductsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Product';
    protected static ?string $pluralModelLabel = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),

                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                FileUpload::make('images')
                    ->label('Product Images')
                    ->multiple()
                    ->image()
                    ->directory('products')
                    ->reorderable()
                    ->nullable(),

                Textarea::make('description')
                    ->rows(5)
                    ->nullable(),

                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('৳'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Toggle::make('is_featured')
                    ->label('Featured')
                    ->default(false),

                Toggle::make('is_stock')
                    ->label('In Stock')
                    ->default(true),

                Toggle::make('on_sale')
                    ->label('On Sale')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                    ImageColumn::make('images')
                    ->label('images')
                    ->circular()
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->label('Category'),

                TextColumn::make('brand.name')
                    ->label('Brand'),

                TextColumn::make('price')
                    ->money('bdt'),

                IconColumn::make('is_stock')
                    ->label('In Stock')
                    ->boolean(),

                IconColumn::make('on_sale')
                    ->label('On Sale')
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),




            ])
            ->filters([
                // Optional filters like category/brand/status
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
            // e.g. ReviewsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
