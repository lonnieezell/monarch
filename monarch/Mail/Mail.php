<?php declare(strict_types=1);

namespace Monarch\Mail;

use Exception;
use Monarch\View;
use Laminas\Mail\Message;
use Monarch\Concerns\IsSingleton;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\FileOptions;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\File as FileTransport;
use Laminas\Mail\Exception\InvalidArgumentException;
use Laminas\Mail\Transport\Sendmail as SendmailTransport;
use Laminas\Mail\Transport\InMemory as InMemoryTransport;
use Laminas\Mail\Header\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;

class Mail
{
    use IsSingleton;

    protected string $viewName;
    protected ?string $layout = '+layout';
    protected array $data = [];
    protected Message $message;
    protected string $transportName = 'sendmail';
    protected TransportInterface $transport;

    /**
     * The entry point for the Mail class.
     * Sets the view name to be used for the email
     * and returns the Mail instance.
     */
    public static function show(string $viewName): Mail
    {
        $instance = self::instance();
        $instance->viewName = $viewName;

        return $instance;
    }

    public function __construct()
    {
        $this->message = new Message();
        $this->transportName = config('mail.default_transport', 'sendmail');
    }

    /**
     * Returns the view name.
     */
    public function viewName(): string
    {
        return $this->viewName ?? '';
    }

    /**
     * Sets the transport to be used for the email.
     * The transport name should be one of the following:
     * sendmail, smtp, file, memory.
     */
    public function useTransport(string $transportName): self
    {
        if (! in_array($transportName, ['sendmail', 'smtp', 'file', 'memory'])) {
            throw new \InvalidArgumentException('Invalid transport specified: '. $transportName);
        }

        $this->transportName = $transportName;

        return $this;
    }

    /**
     * Returns the transport object.
     */
    public function currentTransport(): TransportInterface
    {
        $this->transport = $this->transport();

        return $this->transport;
    }

    /**
     * Sets the layout to be used for the email.
     */
    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Returns the layout name.
     */
    public function layout(): string
    {
        return $this->layout ?? '';
    }

    /**
     * Sets the data to be passed to the view.
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns the data array.
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Returns the message object.
     */
    public function message(): Message
    {
        return $this->message;
    }

    /**
     * Renders the view and returns the HTML.
     */
    public function render(): string
    {
        $renderer = View::factory(APPPATH .'mail');

        $html = $renderer->display($this->viewName, $this->data);

        if ($this->layout) {
            $html = $renderer->display($this->layout, ['content' => $html]);
        }

        return $html;
    }

    /**
     * Renders the view and sends the email.
     *
     * @throws InvalidArgumentException
     * @throws ExceptionInvalidArgumentException
     * @throws Exception
     */
    public function send(): void
    {
        $html = $this->render();

        $this->message->setBody($html);
        $this->message->setEncoding('UTF-8');

        $this->transport()->send($this->message);
    }

    private function isCorrectTransport(): bool
    {
        $testClass = match ($this->transportName) {
            'sendmail' => SendmailTransport::class,
            'smtp' => SmtpTransport::class,
            'file' => FileTransport::class,
            'memory' => InMemoryTransport::class,
            default => null,
        };

        return $this->transport instanceof $testClass;
    }

    /**
     * Determines the transport to use based on the transport name,
     * or uses the default transport, and returns an instance of it.
     */
    private function transport(): TransportInterface
    {
        if (isset($this->transport) && $this->isCorrectTransport()) {
            return $this->transport;
        }

        switch($this->transportName) {
            case 'sendmail':
                $this->transport = new SendmailTransport();
                break;
            case 'smtp':
                $this->transport = new SmtpTransport(
                    new SmtpOptions(config('mail.transport_options.smtp'))
                );
                break;
            case 'file':
                $this->transport = new FileTransport(
                    new FileOptions(config('mail.transport_options.file'))
                );
                break;
            case 'memory':
                $this->transport = new InMemoryTransport();
                break;
            default:
                throw new \Exception('Invalid transport specified.');
        }

        return $this->transport;
    }

    /**
     * Passes methods to the underlying message object.
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->message, $name)) {
            $this->message->$name(...$arguments);

            return $this;
        }

        throw new \BadMethodCallException("Method $name does not exist.");
    }
}
