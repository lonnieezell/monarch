<?php declare(strict_types=1);
use Monarch\Mail\Mail;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\File as FileTransport;
use Laminas\Mail\Transport\Sendmail as SendmailTransport;
use Laminas\Mail\Transport\InMemory as InMemoryTransport;
use Monarch\View;

beforeEach(function () {
    $this->mail = Mail::show('testView');
});
afterEach(function () {
    $this->mail = null;
});

test('show returns instance', function () {
    expect($this->mail)->toBeInstanceOf(Mail::class);
});

test('show sets view name', function () {
    expect($this->mail->viewName())->toBe('testView');
});

test('setLayout sets layout', function () {
    $this->mail->setLayout('newLayout');
    expect($this->mail->layout())->toBe('newLayout');
});

test('setData sets data', function () {
    $data = ['key' => 'value'];
    $this->mail->setData($data);
    expect($this->mail->data())->toBe($data);
});

test('message returns message instance', function () {
    expect($this->mail->message())->toBeInstanceOf(Message::class);
});

test('render returns html', function () {
    $viewMock = mock(View::class);
    $viewMock->shouldReceive('display')->andReturn('<p>Test</p>');
    View::setInstance($viewMock);

    $html = $this->mail->render();
    expect($html)->toContain('<p>Test</p>');
});

test('send sets body and encoding', function () {
    $viewMock = mock(View::class);
    $viewMock->shouldReceive('display')->andReturn('<p>Test</p>');
    View::setInstance($viewMock);

    $this->mail->useTransport('memory');
    $this->mail->send();

    $message = $this->mail->currentTransport()->getLastMessage();

    expect($message->getBody())->toBe('<p>Test</p>');
    expect($message->getEncoding())->toBe('UTF-8');
});

test('can use file transport', function () {
    $this->mail->useTransport('file');
    $transport = $this->mail->currentTransport();

    expect($transport)->toBeInstanceOf(FileTransport::class);
});

test('can use sendmail transport', function () {
    $this->mail->useTransport('sendmail');
    $transport = $this->mail->currentTransport();

    expect($transport)->toBeInstanceOf(SendmailTransport::class);
});

test('can use smtp transport', function () {
    $this->mail->useTransport('smtp');
    $transport = $this->mail->currentTransport();

    expect($transport)->toBeInstanceOf(SmtpTransport::class);
});

test('can use in-memory transport', function () {
    $this->mail->useTransport('memory');
    $transport = $this->mail->currentTransport();

    expect($transport)->toBeInstanceOf(InMemoryTransport::class);
});

test('throws exception for invalid transport', function () {
    $this->mail->useTransport('invalid');
})->throws(InvalidArgumentException::class);
