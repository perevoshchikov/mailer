<?php

namespace Anper\Mailer\Tests\Message;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Message\Message;
use Anper\Mailer\Message\Parts\Address;
use Anper\Mailer\Message\Parts\File;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /**
     * @var Message
     */
    protected $message;

    protected function setUp()
    {
        $this->message = new Message('test');
    }

    protected function tearDown()
    {
        $this->message = null;
    }

    public function testGetId()
    {
        $this->assertEquals('test', $this->message->getId());
    }

    /**
     * @return array
     */
    public function methodsProvider(): array
    {
        return [
            ['Subject', 'hello'],
            ['Subject', null],
            ['Body', 'world'],
            ['Body', null],
            ['Sender', 'hello@world.com', new Address('hello@world.com')],
            ['Sender', new Address('hello@world.com'), new Address('hello@world.com')],
            ['Sender', null],
            ['ReturnPath', 'hello@world.com', new Address('hello@world.com')],
            ['ReturnPath', new Address('hello@world.com'), new Address('hello@world.com')],
            ['ReturnPath', null],
            ['Charset', 'utf-8'],
            ['Charset', null],
            ['ContentType', 'text/html'],
            ['ContentType', null],
            ['Priority', 1],
            ['Priority', null],
        ];
    }

    /**
     * @return array
     */
    public function addressBoxMethodsProvider(): array
    {
        $methods = [
            'From',
            'To',
            'Cc',
            'Bcc',
            'ReplyTo',
        ];

        $result = [];

        $a = function (string $address) {
            return new Address($address);
        };

        foreach ($methods as $method) {
            // single string
            $result[] = [
                $method,
                ['hello@world.com'],
                [$a('hello@world.com')]
            ];

            // many strings
            $result[] = [
                $method,
                ['hello@world.com', 'hello2@world.com'],
                [$a('hello@world.com'), $a('hello2@world.com')]
            ];

            // array of strings
            $result[] = [
                $method,
                [['hello@world.com']],
                [$a('hello@world.com')]
            ];

            // single address
            $result[] = [
                $method,
                [$a('hello@world.com')],
                [$a('hello@world.com')]
            ];

            // many addresses
            $result[] = [
                $method,
                [$a('hello@world.com'), $a('hello2@world.com')],
                [$a('hello@world.com'), $a('hello2@world.com')]
            ];

            // array of addresses
            $result[] = [
                $method,
                [[$a('hello@world.com')]],
                [$a('hello@world.com')]
            ];

            $result[] = [
                $method,
                [],
                [],
            ];

            $result[] = [
                $method,
                [null],
                [],
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function allAddressMethodsProvider(): array
    {
        return [
            ['setFrom'],
            ['setTo'],
            ['setCc'],
            ['setBcc'],
            ['setReplyTo'],

            ['addFrom'],
            ['addTo'],
            ['addCc'],
            ['addBcc'],
            ['addReplyTo'],

            ['setSender'],
            ['setReturnPath'],
        ];
    }

    /**
     * @return array
     */
    public function attachmentsProvider(): array
    {
        $a = function (string $file) {
            return new File($file);
        };

        return [
            [
                [__FILE__], // single string
                [$a(__FILE__)],
            ],
            [
                [__FILE__, __FILE__], // many strings
                [$a(__FILE__), $a(__FILE__)],
            ],
            [
                [[__FILE__]], // array of string
                [$a(__FILE__)],
            ],
            [
                [$a(__FILE__)], // single file
                [$a(__FILE__)],
            ],
            [
                [$a(__FILE__), $a(__FILE__)], // many files
                [$a(__FILE__), $a(__FILE__)],
            ],
            [
                [[$a(__FILE__)]], // array of files
                [$a(__FILE__)],
            ],
            [
                [],
                [],
            ],
            [
                [null],
                [],
            ]
        ];
    }

    /**
     * @return array
     */
    public function headersProvider(): array
    {
        return [
            [
                ['Header: Value'], // single string
                ['Header: Value'],
            ],
            [
                ['Header1: Value1', 'Header2: Value2'], // many strings
                ['Header1: Value1', 'Header2: Value2'],
            ],
            [
                [['Header: Value']], // array of string
                ['Header: Value'],
            ],
            [
                [],
                [],
            ],
            [
                [1], // not string
                [],
            ],
            [
                [null],
                [],
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributesProvider(): array
    {
        return [
            [
                ['foo' => 'bar'],
                ['foo' => 'bar'],
            ],
            [
                [],
                [],
            ],
            [
                null,
                [],
            ]
        ];
    }

    /**
     * @dataProvider methodsProvider
     *
     * @param string $method
     * @param $value
     * @param null $expected
     */
    public function testSetAndGet(string $method, $value, $expected = null)
    {
        $this->assertNull($this->message->{'get' . $method}());

        $this->message->{'set' . $method}($value);
        $returned = $this->message->{'get' . $method}();

        if ($expected) {
            $this->assertEquals($expected, $returned);
        } else {
            $this->assertEquals($value, $returned);
        }
    }

    /**
     * @dataProvider addressBoxMethodsProvider
     *
     * @param string $method
     * @param $value
     * @param array $expected
     */
    public function testSetAndGetAddressBox(string $method, $value, array $expected)
    {
        $this->assertEquals([], $this->message->{'get' . $method}());

        \call_user_func_array(
            [$this->message, 'set' . $method],
            $value
        );

        $returned = $this->message->{'get' . $method}();

        $this->assertEquals($expected, $returned);
    }

    /**
     * @dataProvider addressBoxMethodsProvider
     *
     * @param string $method
     * @param $value
     * @param array $expected
     */
    public function testAddAddressBox(string $method, $value, array $expected)
    {
        $defaultAddress = new Address('default@address.com');
        array_unshift($expected, $defaultAddress);

        $this->message->{'set' . $method}($defaultAddress);

        \call_user_func_array(
            [$this->message, 'add' . $method],
            $value
        );

        $returned = $this->message->{'get' . $method}();

        $this->assertEquals($expected, $returned);
    }

    /**
     * @dataProvider allAddressMethodsProvider
     *
     * @param string $method
     */
    public function testInvalidAddressFormat(string $method)
    {
        $this->expectException(Exception::class);

        $this->message->{$method}('not_address');
    }

    /**
     * @dataProvider attachmentsProvider
     *
     * @param $attachmets
     * @param $expected
     */
    public function testSetAttachments(array $attachmets, array $expected)
    {
        \call_user_func_array([$this->message, 'setAttachments'], $attachmets);

        $this->assertEquals($expected, $this->message->getAttachments());
    }

    /**
     * @dataProvider attachmentsProvider
     *
     * @param $attachmets
     * @param $expected
     */
    public function testAddAttachments(array $attachmets, array $expected)
    {
        $defaultFile = new File(__FILE__);
        array_unshift($expected, $defaultFile);

        $this->message->setAttachments($defaultFile);

        \call_user_func_array([$this->message, 'addAttachments'], $attachmets);

        $this->assertEquals($expected, $this->message->getAttachments());
    }

    /**
     * @param string $method
     */
    public function testSetAttachmentNotFound()
    {
        $this->expectException(Exception::class);

        $this->message->setAttachments('not_file');
    }

    /**
     * @param string $method
     */
    public function testAddAttachmentNotFound()
    {
        $this->expectException(Exception::class);

        $this->message->addAttachments('not_file');
    }

    /**
     * @dataProvider headersProvider
     *
     * @param $headers
     * @param $expected
     */
    public function testSetHeaders(array $headers, array $expected)
    {
        \call_user_func_array([$this->message, 'setHeaders'], $headers);

        $this->assertEquals($expected, $this->message->getHeaders());
    }

    /**
     * @dataProvider headersProvider
     *
     * @param $headers
     * @param $expected
     */
    public function testAddHeaders(array $headers, array $expected)
    {
        $default = 'Default: Value';
        array_unshift($expected, $default);

        $this->message->setHeaders($default);

        \call_user_func_array([$this->message, 'addHeaders'], $headers);

        $this->assertEquals($expected, $this->message->getHeaders());
    }

    /**
     * @dataProvider attributesProvider
     *
     * @param $attributes
     * @param $expected
     */
    public function testSetAttributes($attributes, array $expected)
    {
        \call_user_func([$this->message, 'setAttributes'], $attributes);

        $this->assertEquals($expected, $this->message->getAttributes());
    }

    /**
     * @dataProvider attributesProvider
     *
     * @param $attributes
     * @param $expected
     */
    public function testAddAttributes($attributes, array $expected)
    {
        if ($attributes === null) {
            $this->assertTrue(true);
            return;
        }

        $expected['default'] = 'value';

        $this->message->setAttributes([
            'default' => 'value',
        ]);

        \call_user_func([$this->message, 'addAttributes'], $attributes);

        $this->assertEquals($expected, $this->message->getAttributes());
    }

    public function testToArray()
    {
        $data =  [
            'from'         => [new Address('from@test.com')],
            'to'           => [new Address('to@test.com')],
            'sender'       => new Address('sender@test.com'),
            'cc'           => [new Address('cc@test.com')],
            'bcc'          => [new Address('bcc@test.com')],
            'reply_to'     => [new Address('reply_to@test.com')],
            'attachments'  => [new File(__FILE__)],
            'return_path'  => new Address('return_path@test.com'),
            'subject'      => 'subject',
            'body'         => 'body',
            'content_type' => 'text/html',
            'charset'      => 'utf-8',
            'priority'     => 1,
            'headers'      => ['Header: Value'],
            'attributes'   => ['foo' => 'bar'],
        ];

        $this->message
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setCc($data['cc'])
            ->setBcc($data['bcc'])
            ->setReplyTo($data['reply_to'])
            ->setSender($data['sender'])
            ->setReturnPath($data['return_path'])
            ->setSubject($data['subject'])
            ->setBody($data['body'])
            ->setCharset($data['charset'])
            ->setContentType($data['content_type'])
            ->setPriority($data['priority'])
            ->setHeaders($data['headers'])
            ->setAttributes($data['attributes'])
            ->setAttachments($data['attachments']);

        $this->assertEquals($data, $this->message->toArray());
    }

    /**
     * @return array
     */
    public function messageDataProvider(): array
    {
        $email = 'test@test.test';

        $a = function () use ($email) {
            return new Address($email);
        };

        $f = function () {
            return new File(__FILE__);
        };

        $data = [
            ['subject', 'subject', 'subject'],
            ['subject', null, null],
            ['body', 'body', 'body'],
            ['body', null, null],
            ['priority', 1, 1],
            ['priority', null, null],
            ['content_type', 'text/html', 'text/html'],
            ['content_type', null, null],
            ['charset', 'utf-8', 'utf-8'],
            ['charset', null, null],
            ['sender', $email, $a()],
            ['sender', null, null],
            ['return_path', $email, $a()],
            ['return_path', null, null],
            ['attachments', null, []],
            ['attachments', [], []],
            ['attachments', $f(), [$f()]],
            ['attachments', [$f()], [$f()]],
            ['attachments', __FILE__, [$f()]],
            ['attachments', [__FILE__], [$f()]],
            ['headers', 'Header: Value', ['Header: Value']],
            ['headers', ['Header: Value'], ['Header: Value']],
            ['headers', [], []],
            ['headers', null, []],
            ['attributes', null, []],
            ['attributes', [], []],
            ['attributes', ['key' => 'value'], ['key' => 'value']],
        ];

        foreach (['from', 'to', 'cc', 'bcc', 'reply_to'] as $option) {
            $data[] = [$option, null, []];
            $data[] = [$option, [], []];
            $data[] = [$option, $email, [$a()]];
            $data[] = [$option, $a(), [$a()]];
            $data[] = [$option, [$a()], [$a()]];
            $data[] = [$option, [$email], [$a()]];
        }

        return $data;
    }

    /**
     * @dataProvider messageDataProvider
     *
     * @param string $key
     * @param mixed $value
     * @param mixed $expectedValue
     *
     * @throws Exception
     */
    public function testFill(string $key, $value, $expectedValue)
    {
        $message = new Message('test');

        $expected = $message->toArray();
        $expected[$key] = $expectedValue;
        $data[$key] = $value;

        $message->fill($data);

        $this->assertEquals($expected, $message->toArray());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function invalidMessageDataProvider(): array
    {
        $notEmail = 'not_email';
        $notString = $notArray = new \DateTime();

        return [
            ['from', $notString],
            ['from', $notEmail],
            ['to', $notString],
            ['to', $notEmail],
            ['cc', $notString],
            ['cc', $notEmail],
            ['bcc', $notString],
            ['bcc', $notEmail],
            ['reply_to', $notString],
            ['reply_to', $notEmail],
            ['subject', $notString],
            ['body', $notString],
            ['priority', $notString],
            ['content_type', $notString],
            ['charset', $notString],
            ['sender', $notEmail],
            ['sender', $notString],
            ['return_path', $notEmail],
            ['return_path', $notString],
            ['attachments', $notString],
            ['attachments', $notEmail],
            ['attachments', $notEmail],
            ['headers',     $notArray],
            ['headers',     $notString],
            ['attributes',  $notArray],
        ];
    }

    /**
     * @dataProvider invalidMessageDataProvider
     *
     * @param string $key
     * @param $value
     *
     * @throws Exception
     */
    public function testInvalidFill(string $key, $value)
    {
        $this->expectException(Exception::class);

        (new Message('test'))->fill([$key => $value]);
    }
}
