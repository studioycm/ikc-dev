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
use Illuminate\Support\Facades\Notification as LaravelNotification;
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

                        // Probe SMTP connectivity first to avoid long timeouts in UI.
                        $portsToProbe = array_values(array_unique(array_filter([
                            (int)($config['port'] ?? 0),
                            2525,
                            465,
                            587,
                        ])));
                        $probe = self::smtpProbe((string)$config['host'], $portsToProbe, 2.0);

                        $configuredPort = (int)($config['port'] ?? 0);
                        $configuredReachable = $configuredPort > 0 && ($probe['ports'][$configuredPort]['ok'] ?? false) === true;

                        if (!$configuredReachable) {
                            $hints = self::buildMailHints($config, $probe);

                            Log::warning('SMTP probe indicates configured host:port is unreachable.', [
                                'mail' => $config,
                                'probe' => $probe,
                                'user_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Mail connection unreachable')
                                ->body('Could not reach the configured SMTP endpoint before sending. Please verify your SendGrid SMTP settings.'
                                    . '<br>' . self::formatMailDiagnosticInfo($config)
                                    . '<br>' . self::formatProbe($probe)
                                    . ($hints !== '' ? '<br>' . $hints : ''))
                                ->danger()
                                ->persistent()
                                ->send();

                            return;
                        }

                        try {
                            LaravelNotification::sendNow($record, new TestMailNotification($record));

                            Notification::make()
                                ->title('Email dispatched')
                                ->body('A test email was dispatched to ' . $record->email
                                    . '<br>' . self::formatMailDiagnosticInfo($config)
                                    . '<br>' . self::formatProbe($probe))
                                ->success()
                                ->send();
                        } catch (TransportExceptionInterface $e) {
                            Log::error('SMTP transport error during test mail.', [
                                'exception' => $e,
                                'mail' => $config,
                                'probe' => $probe,
                                'user_id' => $record->id,
                            ]);

                            $hints = self::buildMailHints($config, $probe);

                            Notification::make()
                                ->title('Mail connection failed')
                                ->body('Could not connect to the SMTP server. Please verify your SendGrid SMTP settings.'
                                    . '<br>' . self::formatMailDiagnosticInfo($config)
                                    . '<br>' . self::formatProbe($probe)
                                    . ($hints !== '' ? '<br>' . $hints : ''))
                                ->danger()
                                ->persistent()
                                ->send();
                        } catch (Throwable $e) {
                            Log::error('Unexpected error during test mail.', [
                                'exception' => $e,
                                'mail' => $config,
                                'probe' => $probe,
                                'user_id' => $record->id,
                            ]);

                            $hints = self::buildMailHints($config, $probe);

                            Notification::make()
                                ->title('Mail error')
                                ->body('An unexpected error occurred while sending the test email.'
                                    . '<br>' . self::formatMailDiagnosticInfo($config)
                                    . '<br>' . self::formatProbe($probe)
                                    . ($hints !== '' ? '<br>' . $hints : ''))
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

        // Queue diagnostics
        $queueDefault = (string)(config('queue.default') ?? 'sync');
        $queueDriver = (string)(config("queue.connections.$queueDefault.driver") ?? '');
        $queueName = (string)(config("queue.connections.$queueDefault.queue") ?? (string)(config('queue.queue', 'default')));

        return [
            'default' => $default,
            'transport' => $transport,
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'queue_default' => $queueDefault,
            'queue_driver' => $queueDriver,
            'queue_name' => $queueName,
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
            'queue.default=' . (string)($config['queue_default'] ?? ''),
            'queue.driver=' . (string)($config['queue_driver'] ?? ''),
            'queue.name=' . (string)($config['queue_name'] ?? ''),
        ];

        return implode(' | ', $parts);
    }

    /**
     * Probe SMTP connectivity for a host over given ports.
     * Returns an array: ['host' => string, 'ports' => [port => ['ok' => bool, 'time_ms' => int, 'error' => string|null]]]
     */
    protected static function smtpProbe(string $host, array $ports, float $timeoutSeconds = 2.0): array
    {
        $result = [
            'host' => $host,
            'ports' => [],
        ];

        $ports = array_values(array_unique(array_filter(array_map('intval', $ports), fn($p) => $p > 0)));

        foreach ($ports as $port) {
            $start = microtime(true);
            $ok = false;
            $error = null;

            try {
                $remote = sprintf('tcp://%s:%d', $host, $port);
                $context = stream_context_create([
                    'socket' => [
                        'tcp_nodelay' => true,
                    ],
                ]);
                $errno = 0;
                $errstr = '';
                $fp = @stream_socket_client($remote, $errno, $errstr, $timeoutSeconds, STREAM_CLIENT_CONNECT, $context);
                if ($fp !== false) {
                    $ok = true;
                    fclose($fp);
                } else {
                    $ok = false;
                    $error = $errstr !== '' ? $errstr : 'Connection failed';
                }
            } catch (\Throwable $e) {
                $ok = false;
                $error = $e->getMessage();
            }

            $timeMs = (int)round((microtime(true) - $start) * 1000);

            $result['ports'][$port] = [
                'ok' => $ok,
                'time_ms' => $timeMs,
                'error' => $ok ? null : $error,
            ];
        }

        return $result;
    }

    /**
     * Format the probe output for inclusion in a UI message.
     */
    protected static function formatProbe(array $probe): string
    {
        if (!isset($probe['ports']) || $probe['ports'] === []) {
            return 'probe: none';
        }

        $parts = [];
        foreach ($probe['ports'] as $port => $info) {
            $status = ($info['ok'] ?? false) ? 'up' : 'down';
            $lat = isset($info['time_ms']) ? $info['time_ms'] . 'ms' : '';
            $extra = '';
            if (!($info['ok'] ?? false) && !empty($info['error'])) {
                $extra = '(' . strtok((string)$info['error'], "\n") . ')';
            }
            $parts[] = $port . '=' . $status . ($lat !== '' ? '(' . $lat . ')' : '') . ($extra !== '' ? ' ' . $extra : '');
        }

        return 'probe: ' . implode(' ', $parts);
    }

    /**
     * Provide human hints for typical SendGrid SMTP configuration.
     */
    protected static function buildMailHints(array $config, array $probe): string
    {
        $host = (string)($config['host'] ?? '');
        $port = (int)($config['port'] ?? 0);
        $encryption = trim((string)($config['encryption'] ?? ''));

        $hints = [];
        if ($host !== '' && str_contains($host, 'sendgrid.net')) {
            // Recommend TLS on 587/2525, SSL on 465
            if (in_array($port, [587, 2525], true) && $encryption === '') {
                $hints[] = 'Hint: For SendGrid on port ' . $port . ', set MAIL_ENCRYPTION=tls.';
            }
            if ($port === 465 && !in_array($encryption, ['ssl', 'tls'], true)) {
                $hints[] = 'Hint: For SendGrid on port 465, set MAIL_ENCRYPTION=ssl.';
            }
            // Username must be literally "apikey"
            if ((string)($config['username'] ?? '') !== 'apikey') {
                $hints[] = 'Hint: SendGrid SMTP username must be literally "apikey" and password your API key.';
            }
        }

        // If configured port is down but another common port is up, suggest switching.
        $ports = $probe['ports'] ?? [];
        if ($port > 0 && isset($ports[$port]) && ($ports[$port]['ok'] ?? false) === false) {
            foreach ([2525, 587, 465] as $alt) {
                if ($alt !== $port && isset($ports[$alt]) && ($ports[$alt]['ok'] ?? false) === true) {
                    $suggestEnc = in_array($alt, [587, 2525], true) ? 'tls' : 'ssl';
                    $hints[] = 'Hint: Port ' . $port . ' seems unreachable, but ' . $alt . ' is reachable. Try MAIL_PORT=' . $alt . ' and MAIL_ENCRYPTION=' . $suggestEnc . '.';
                    break;
                }
            }
        }

        return implode(' ', $hints);
    }
}
