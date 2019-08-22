<?php

namespace Anper\Mailer\Message;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Message\Parts\Address;
use Anper\Mailer\Message\Parts\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Message
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $subject;

    /**
     * @var string|null
     */
    protected $body;

    /**
     * @var Address[]
     */
    protected $from = [];

    /**
     * @var Address[]
     */
    protected $to = [];

    /**
     * @var Address[]
     */
    protected $cc = [];

    /**
     * @var Address[]
     */
    protected $bcc = [];

    /**
     * @var Address[]
     */
    protected $replyTo = [];

    /**
     * @var Address|null
     */
    protected $sender;

    /**
     * @var Address|null
     */
    protected $returnPath;

    /**
     * @var File[]
     */
    protected $attachments = [];

    /**
     * @var string|null
     */
    protected $contentType;

    /**
     * @var string|null
     */
    protected $charset;

    /**
     * @var int|null
     */
    protected $priority;

    /**
     * @var string[]
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param array $data
     *
     * @return Message
     * @throws Exception
     */
    public function fill(array $data): self
    {
        $file = File::class;
        $address = Address::class;
        $addressBoxType = ['null', 'string', $address, 'string[]', $address . '[]'];

        $resolver = (new OptionsResolver())
            ->setDefaults($this->toArray())
            ->setAllowedTypes('from', $addressBoxType)
            ->setAllowedTypes('to', $addressBoxType)
            ->setAllowedTypes('cc', $addressBoxType)
            ->setAllowedTypes('bcc', $addressBoxType)
            ->setAllowedTypes('reply_to', $addressBoxType)
            ->setAllowedTypes('subject', ['null', 'string'])
            ->setAllowedTypes('body', ['null', 'string'])
            ->setAllowedTypes('priority', ['null', 'int'])
            ->setAllowedTypes('sender', ['null', 'string', $address])
            ->setAllowedTypes('return_path', ['null', 'string', $address])
            ->setAllowedTypes('content_type', ['null', 'string'])
            ->setAllowedTypes('charset', ['null', 'string'])
            ->setAllowedTypes('headers', ['null', 'string', 'string[]'])
            ->setAllowedTypes('attributes', ['null', 'array'])
            ->setAllowedTypes('attachments', ['null', 'string', $file, 'string[]', $file . '[]']);

        foreach (['from', 'to', 'cc', 'bcc', 'reply_to', 'attachments', 'headers'] as $option) {
            $resolver->setNormalizer($option, function ($options, $value) {
                return \is_array($value) && empty($value) ? null : $value;
            });
        }

        try {
            $data = $resolver->resolve($data);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(), 0, $exception);
        }

        $this
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

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     *
     * @return Message
     */
    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     *
     * @return Message
     */
    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return Address[]
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function setFrom(...$addresses): self
    {
        return $this->setAddressList($this->from, $addresses);
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function addFrom(...$addresses): self
    {
        return $this->addAddressList($this->from, $addresses);
    }

    /**
     * @return Address[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function setTo(...$addresses): self
    {
        return $this->setAddressList($this->to, $addresses);
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function addTo(...$addresses): self
    {
        return $this->addAddressList($this->to, $addresses);
    }

    /**
     * @return Address[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function setCc(...$addresses): self
    {
        return $this->setAddressList($this->cc, $addresses);
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function addCc(...$addresses): self
    {
        return $this->addAddressList($this->cc, $addresses);
    }

    /**
     * @return Address[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function setBcc(...$addresses): self
    {
        return $this->setAddressList($this->bcc, $addresses);
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function addBcc(...$addresses): self
    {
        return $this->addAddressList($this->bcc, $addresses);
    }

    /**
     * @return Address[]
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function setReplyTo(...$addresses): self
    {
        return $this->setAddressList($this->replyTo, $addresses);
    }

    /**
     * @param Address[]|string[]|Address|string|null ...$addresses
     *
     * @return Message
     * @throws Exception
     */
    public function addReplyTo(...$addresses): self
    {
        return $this->addAddressList($this->replyTo, $addresses);
    }

    /**
     * @return Address|null
     */
    public function getSender(): ?Address
    {
        return $this->sender;
    }

    /**
     * @param Address|string|null $address
     *
     * @return Message
     * @throws Exception
     */
    public function setSender($address): self
    {
        return $this->setAddress($this->sender, $address);
    }

    /**
     * @return Address|null
     */
    public function getReturnPath(): ?Address
    {
        return $this->returnPath;
    }

    /**
     * @param Address|string|null $address
     *
     * @return Message
     * @throws Exception
     */
    public function setReturnPath($address): self
    {
        return $this->setAddress($this->returnPath, $address);
    }

    /**
     * @return File[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param File[]|string[]|File|string|null ...$attachments
     *
     * @return Message
     * @throws Exception
     */
    public function setAttachments(...$attachments): self
    {
        $this->attachments = [];

        return $this->addAttachments(...$attachments);
    }

    /**
     * @param File[]|string[]|File|string|null ...$attachments
     *
     * @return Message
     * @throws Exception
     */
    public function addAttachments(...$attachments): self
    {
        $this->attachments = \array_merge($this->attachments, File::createArray(
            $this->prepareArray($attachments)
        ));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @param string|null $contentType
     *
     * @return Message
     */
    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * @param string|null $charset
     *
     * @return Message
     */
    public function setCharset(?string $charset): self
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $priority
     *
     * @return Message
     */
    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string|string[]|null ...$headers
     *
     * @return Message
     */
    public function setHeaders(...$headers): self
    {
        $this->headers = [];

        return $this->addHeaders(...$headers);
    }

    /**
     * @param string|string[]|null ...$headers
     *
     * @return Message
     */
    public function addHeaders(...$headers): self
    {
        $prepared = \array_filter($this->prepareArray($headers), function ($header) {
            return \is_string($header);
        });

        $this->headers = \array_merge($this->headers, $prepared);

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array|null $attributes
     *
     * @return Message
     */
    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes ?? [];

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return Message
     */
    public function addAttributes(array $attributes): self
    {
        $this->attributes = \array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'from'         => $this->getFrom(),
            'to'           => $this->getTo(),
            'sender'       => $this->getSender(),
            'cc'           => $this->getCc(),
            'bcc'          => $this->getBcc(),
            'reply_to'     => $this->getReplyTo(),
            'attachments'  => $this->getAttachments(),
            'return_path'  => $this->getReturnPath(),
            'subject'      => $this->getSubject(),
            'body'         => $this->getBody(),
            'content_type' => $this->getContentType(),
            'charset'      => $this->getCharset(),
            'priority'     => $this->getPriority(),
            'headers'      => $this->getHeaders(),
            'attributes'   => $this->getAttributes(),
        ];
    }

    /**
     * @param array $prop
     * @param array $addresses
     *
     * @return Message
     * @throws Exception
     */
    protected function setAddressList(array &$prop, array $addresses): self
    {
        $prop = [];

        return $this->addAddressList($prop, $addresses);
    }

    /**
     * @param array $prop
     * @param array $addresses
     *
     * @return Message
     * @throws Exception
     */
    protected function addAddressList(array &$prop, array $addresses): self
    {
        $prop = \array_merge($prop, Address::createArray(
            $this->prepareArray($addresses)
        ));

        return $this;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function prepareArray(array $array): array
    {
        if (\count($array) === 1) {
            if (\is_array($array[0])) {
                return $array[0];
            }

            if ($array[0] === null) {
                return [];
            }
        }

        return $array;
    }

    /**
     * @param mixed $prop
     * @param Address|string|null $address
     *
     * @return Message
     * @throws Exception
     */
    protected function setAddress(&$prop, $address): self
    {
        $prop = $address === null ? null : Address::create($address);

        return $this;
    }
}
