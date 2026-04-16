<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\PrevUser;
use App\Models\User;
use App\Notifications\UserMessageNotification;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('prevUser');
    }

    public static function getModelLabel(): string
    {
        return __('System User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('System Users');
    }

    public static function getNavigationGroup(): string
    {
        return __('Authorisation Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('System Users');
    }

    protected static ?int $navigationSort = 98;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return static::getModel()::count();
    //    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required(),
                Forms\Components\Select::make('prev_user_id')
                    ->label(__('Legacy User'))
                    ->nullable()
                    ->placeholder(__('—'))
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search, 50))
                    ->getOptionLabelUsing(function ($value) {
                        if (!$value) {
                            return null;
                        }

                        // Optimization: Select only columns needed for the 'search_label' accessor
                        return PrevUser::query()
                            ->select(['id', 'first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'phone', 'email'])
                            ->find($value)
                            ?->search_label;
                    })
                    ->unique(ignoreRecord: true),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label(__('Verified At'))
                    ->native(false)
                    ->displayFormat('d/m/Y H:i'),
                Forms\Components\Toggle::make('edit_password')
                    ->label(__('Edit Password'))
                    ->default(false)
                    ->dehydrated(false)
                    ->live(),
                Forms\Components\TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->hidden(fn(string $operation, Get $get, ?User $record): bool => $operation === 'edit' && (auth()->id() === $record?->id || $get('edit_password') === false))
                    ->revealable(),
                Forms\Components\Select::make('roles')
                    ->label(__('Roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'prevUser' => function ($q) {
                            // This runs entirely on the Legacy DB, so it works perfectly.
                            // It adds a 'dogs_count' attribute to the prevUser model.
                            $q->withCount('dogs');
                        },
                    ]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->icon('heroicon-o-envelope')
                    ->iconColor('warning')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('prevUser.name')
                    ->label(__('Legacy User'))
                    ->placeholder('-')
                    ->sortable(false)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Step A: Search the Legacy DB first
                        // We reuse the 'searchName' scope you already have in PrevUser model!
                        $matchingLegacyIds = PrevUser::query()->searchName($search)
                            ->pluck('id')
                            ->toArray(); // Important: convert to array for whereIn

                        // Step B: Filter the System DB using those IDs
                        // If no matches found in legacy, passing empty array returns no results (correct)
                        return $query->whereIn('prev_user_id', $matchingLegacyIds);
                    })
                    ->description(function (User $record) {
                        $legacy = $record->prevUser;

                        if (!$legacy) {
                            return null;
                        }

                        // Combine Phone and Email, filtering out empty values
                        return collect([$legacy->normalised_phone, $legacy->email, "#{$legacy->id}"])
                            ->filter()
                            ->join(' • '); // Separator dot
                    })
                    ->sortable(false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('prevUser.dogs_count')
                    ->label(__('dog/model/general.labels.plural'))
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->default(0)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('Verified'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Role'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'success',
                        'panel_user' => 'warning',
                        'admin' => 'danger',
                        default => 'info',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder(__('Search (ID, Name)'))
            ->searchOnBlur()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('email_verification')
                    ->label(__('Verify'))
                    ->button()
                    ->tooltip(__('Send email verification link'))
                    ->color(Color::hex('#ec8200'))
                    ->icon('heroicon-o-shield-check')
                    ->action(function (User $user) {
                        $notification = app(VerifyEmail::class);
                        $notification->url = Filament::getVerifyEmailUrl($user);
                        $user->notify($notification);
                        Notification::make()
                            ->title(__('Email verification link sent'))
                            ->body(__('Email verification link sent to :email<br>:url', [
                                'email' => $user->email,
                                'url' => $notification->url,
                            ]))
                            ->success()
                            ->icon('heroicon-o-shield-check')
                            ->iconColor('primary')
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\Action::make('email_verified')
                    ->label(__('Verified'))
                    ->button()
                    ->tooltip(__('Mark as verified'))
                    ->color(Color::hex('#10b138'))
                    ->icon('heroicon-o-check')
                    ->action(function (User $user) {
                        $user->markEmailAsVerified();
                    }),
                Tables\Actions\Action::make('send_db_notice')
                    ->label(__('Notify'))
                    ->button()
                    ->tooltip(__('Send a database notification to this user'))
                    ->color('gray')
                    ->icon('heroicon-o-bell')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->label(__('Subject'))
                            ->required()
                            ->maxLength(150),
                        Forms\Components\RichEditor::make('body')
                            ->label(__('Message'))
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h1',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('editor-attachments')
                            ->fileAttachmentsVisibility('public')
                            ->disableGrammarly()
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->notify(new UserMessageNotification(
                            subject: (string)$data['subject'],
                            body: (string)$data['body'],
                            channels: ['database'],
                        ));

                        Notification::make()
                            ->title(__('Notification created'))
                            ->body(__('A database notification was created for :email', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('send_email')
                    ->label(__('Send Email'))
                    ->button()
                    ->tooltip(__('Send an email to this user'))
                    ->color('primary')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->label(__('Subject'))
                            ->required()
                            ->maxLength(150),
                        Forms\Components\RichEditor::make('body')
                            ->label(__('Message'))
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h1',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('editor-attachments')
                            ->fileAttachmentsVisibility('public')
                            ->disableGrammarly()
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        LaravelNotification::sendNow($record, new UserMessageNotification(
                            subject: (string)$data['subject'],
                            body: (string)$data['body'],
                            channels: ['mail'],
                        ));

                        Notification::make()
                            ->title(__('Email sent'))
                            ->body(__('Email sent to :email', ['email' => $record->email]))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_send_email')
                        ->label(__('Send Email'))
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\TextInput::make('subject')
                                ->label(__('Subject'))
                                ->required()
                                ->maxLength(150),
                            Forms\Components\RichEditor::make('body')
                                ->label(__('Message'))
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'h1',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('editor-attachments')
                                ->fileAttachmentsVisibility('public')
                                ->disableGrammarly()
                                ->columnSpanFull()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $notification = new UserMessageNotification(
                                subject: (string)$data['subject'],
                                body: (string)$data['body'],
                                channels: ['mail'],
                            );

                            LaravelNotification::sendNow($records, $notification);

                            Notification::make()
                                ->title(__('Emails sent'))
                                ->body(__('Email sent to :count users', ['count' => $records->count()]))
                                ->success()
                                ->send();
                        }),
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
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
