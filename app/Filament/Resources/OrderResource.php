<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $modelLabel = 'Order';

  public static function form(Form $form): Form
{
    return $form
        ->schema([
            Select::make('user_id')
                ->label('Customer')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            TextInput::make('grand_total')
                ->label('Grand Total')
                ->numeric()
                ->required(),

            Select::make('payment_method')
                ->options([
                    'cash' => 'Cash',
                    'card' => 'Card',
                    'bkash' => 'bKash',
                    'nagad' => 'Nagad',
                ])
                ->nullable(),

            Select::make('payment_status')
                ->options([
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'pending' => 'Pending',
                ])
                ->nullable(),

            Select::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->default('new')
                ->required(),

            TextInput::make('currency')
                ->default('BDT')
                ->required(),

            TextInput::make('shipping_amount')
                ->label('Shipping Amount')
                ->numeric()
                ->nullable(),

            TextInput::make('shipping_method')
                ->nullable(),



            TextInput::make('notes')
                ->nullable()
                ->columnSpanFull(),

            Section::make('Order Items')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->reactive(),

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minvalue(1),

                            TextInput::make('unit_ammount')
                                ->numeric()
                                ->required(),
                                TextInput::make('total_ammount')
                                ->numeric()
                                ->required(),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->createItemButtonLabel('Add Item'),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
    ->columns([
        TextColumn::make('id')->sortable(),
        TextColumn::make('user.name')->label('Customer'),
        TextColumn::make('grand_total')->label('Grand Total'),
        TextColumn::make('status')->badge(),
        TextColumn::make('payment_status'),
        TextColumn::make('created_at')->dateTime('d M, Y h:i A'),

        TextColumn::make('products')
            ->label('Products')
            ->getStateUsing(function (Order $record) {
                return $record->items->pluck('product.name')->join(', ');
            })
            ->wrap()
            ->limit(50),
    ])
            ->filters([
                // Filters can be added here
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            AddressRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
