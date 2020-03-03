<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $senderId;

    /**
     * @ORM\Column(type="integer")
     */
    private $receiverId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $msgContent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $msgStatus;

    /**
     * @ORM\Column(type="date")
     */
    private $msgDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $msgType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function setSenderId(int $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getReceiverId(): ?int
    {
        return $this->receiverId;
    }

    public function setReceiverId(int $receiverId): self
    {
        $this->receiverId = $receiverId;

        return $this;
    }

    public function getMsgContent(): ?string
    {
        return $this->msgContent;
    }

    public function setMsgContent(string $msgContent): self
    {
        $this->msgContent = $msgContent;

        return $this;
    }

    public function getMsgStatus(): ?string
    {
        return $this->msgStatus;
    }

    public function setMsgStatus(?string $msgStatus): self
    {
        $this->msgStatus = $msgStatus;

        return $this;
    }

    public function getMsgDate(): ?\DateTimeInterface
    {
        return $this->msgDate;
    }

    public function setMsgDate(\DateTimeInterface $msgDate): self
    {
        $this->msgDate = $msgDate;

        return $this;
    }

    public function getMsgType(): ?string
    {
        return $this->msgType;
    }

    public function setMsgType(string $msgType): self
    {
        $this->msgType = $msgType;

        return $this;
    }

    public function getAttachments(): ?string
    {
        return $this->attachments;
    }

    public function setAttachments(?string $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }


}
