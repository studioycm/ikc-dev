<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Notifications\TestMailNotification;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

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
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-o-envelope')
                    ->iconColor('warning')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\BooleanColumn::make('email_verified_at')
                    ->label('Verified')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'success',
                        'panel_user' => 'warning',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder('Search (ID, Name)')
            ->searchOnBlur()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('email_verification')
                    ->label('Verify')
                    ->button()
                    ->tooltip('Send email verification link')
                    ->color(Color::hex('#ec8200'))
                    ->icon('heroicon-o-shield-check')
                    ->action(function (User $user) {
                        // $notification = new VerifyEmail;
                        // $notification->toMail($user);
                        // $user->sendEmailVerificationNotification();
                        $notification = app(VerifyEmail::class);
                        $notification->url = Filament::getVerifyEmailUrl($user);
                        $user->notify($notification);
                        Notification::make()
                            ->title('Email verification link sent')
                            ->body('Email verification link sent to ' . $user->email .
                                '<br>' . $notification->url)
                            ->success()
                            ->icon('heroicon-o-shield-check')
                            ->iconColor('primery')
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\Action::make('email_verified')
                    ->label('Verified')
                    ->button()
                    ->tooltip('Mark as verified')
                    ->color(Color::hex('#10b138'))
                    ->icon('heroicon-o-check')
                    ->action(function (User $user) {
                        $user->markEmailAsVerified();
                        // $user->email_verified_at = now();
                        // $user->save();
                    }),
                Tables\Actions\Action::make('send_test_email')
                    ->label('Send test email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $config = self::getMailDiagnosticInfo();

                        try {
                            $record->notify(new TestMailNotification($record));

                            Notification::make()
                                ->title('Email dispatched')
                                ->body('A test email was dispatched to ' . $record->email
                                    . '<br>' . self::formatMailDiagnosticInfo($config))
                                ->success()
                                ->send();
                        } catch (TransportExceptionInterface $e) {
                            Log::error('SMTP transport error during test mail.', [
                                'exception' => $e,
                                'mail' => $config,
                                'user_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Mail connection failed')
                                ->body('Could not connect to the SMTP server. Please verify your SendGrid SMTP settings.'
                                    . '<br>' . self::formatMailDiagnosticInfo($config))
                                ->danger()
                                ->persistent()
                                ->send();
                        } catch (Throwable $e) {
                            Log::error('Unexpected error during test mail.', [
                                'exception' => $e,
                                'mail' => $config,
                                'user_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Mail error')
                                ->body('An unexpected error occurred while sending the test email.'
                                    . '<br>' . self::formatMailDiagnosticInfo($config))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

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
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    protected static function getMailDiagnosticInfo(): array
    {
        $default = (string)(config('mail.default') ?? 'smtp');
        $mailerKey = $default !== '' ? $default : 'smtp';

        $transport = (string)(config("mail.mailers.$mailerKey.transport") ?? '');
        $host = (string)(config("mail.mailers.$mailerKey.host") ?? config('mail.mailers.smtp.host'));
        $port = (int)(config("mail.mailers.$mailerKey.port") ?? (int)(config('mail.mailers.smtp.port') ?? 0));
        $encryption = (string)(config("mail.mailers.$mailerKey.encryption") ?? config('mail.mailers.smtp.encryption'));
        $username = (string)(config("mail.mailers.$mailerKey.username") ?? config('mail.mailers.smtp.username'));
        $fromAddress = (string)(config('mail.from.address') ?? '');
        $fromName = (string)(config('mail.from.name') ?? '');

        return [
            'default' => $default,
            'transport' => $transport,
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
        ];
    }

    protected static function formatMailDiagnosticInfo(array $config): string
    {
        $from = trim((string)($config['from_address'] ?? ''));
        $fromName = trim((string)($config['from_name'] ?? ''));
        $fromDisplay = $fromName !== '' ? $from . ' (' . $fromName . ')' : $from;

        $parts = [
            'default=' . (string)($config['default'] ?? ''),
            'transport=' . (string)($config['transport'] ?? ''),
            'host=' . (string)($config['host'] ?? ''),
            'port=' . (string)($config['port'] ?? ''),
            'encryption=' . (string)($config['encryption'] ?? ''),
            'username=' . (string)($config['username'] ?? ''),
            'from=' . $fromDisplay,
        ];

        return implode(' | ', $parts);
    }
}
