<?php

namespace V3s3Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

/**
 * Store
 *
 * @ORM\Table(name="store", indexes={@ORM\Index(name="hash_name", columns={"hash_name"})})
 * @ORM\Entity(repositoryClass="V3s3Bundle\Repository\V3s3Repository")
 */
class V3s3 {
	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 1;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="integer", nullable=true)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="date_time", type="string", length=25, nullable=true)
     */
    private $date_time;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="hash_name", type="string", length=40, nullable=true)
     */
    private $hash_name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="blob", length=65535, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="blob", length=16777215, nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     */
    private $mime_type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp_deleted", type="integer", nullable=true)
     */
    private $timestamp_deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="date_time_deleted", type="string", length=25, nullable=true)
     */
    private $date_time_deleted;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_deleted_from", type="string", length=15, nullable=true)
     */
    private $ip_deleted_from;



	public function __construct(Array $attr = []) 	{
		if(!empty($attr)) {
			$this->fromArray($attr);
		}
	}

	public function fromArray(Array $attr) {
		if(!is_array($attr)) {
			return;
		}

		foreach($attr as $key=>$value) {
			if(method_exists($this, 'set'.$key)) {
				$this->{'set'.$key}($value);
			}
		}
	}

	public function castEntityObjectToArray() {
		$propertyNormalizer = new PropertyNormalizer();
		$propertyNormalizer->setCallbacks(
			[
				'name'=>'stream_get_contents', // cast blob fields from stream resource to string
				'data'=>'stream_get_contents', // cast blob fields from stream resource to string
			]
		);
		return $propertyNormalizer->normalize($this);
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getid() {
		return $this->id;
	}

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     *
     * @return V3s3
     */
    public function settimestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer
     */
    public function gettimestamp() {
        return $this->timestamp;
    }

    /**
     * Set date_time
     *
     * @param string $date_time
     *
     * @return V3s3
     */
    public function setdate_time($date_time) {
        $this->date_time = $date_time;

        return $this;
    }

    /**
     * Get date_time
     *
     * @return string
     */
    public function getdate_time() {
        return $this->date_time;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return V3s3
     */
    public function setip($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getip() {
        return $this->ip;
    }

    /**
     * Set hash_name
     *
     * @param string $hash_name
     *
     * @return V3s3
     */
    public function sethash_name($hash_name) {
        $this->hash_name = $hash_name;

        return $this;
    }

    /**
     * Get hash_name
     *
     * @return string
     */
    public function gethash_name() {
        return $this->hash_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return V3s3
     */
    public function setname($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getname() {
		if(is_resource($this->name) && (get_resource_type($this->name) == 'stream')) {
			return stream_get_contents($this->name);
		}

        return $this->name;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return V3s3
     */
    public function setdata($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getdata() {
		if(is_resource($this->data) && (get_resource_type($this->data) == 'stream')) {
			return stream_get_contents($this->data);
		}

        return $this->data;
    }

    /**
     * Set mime_type
     *
     * @param string $mime_type
     *
     * @return V3s3
     */
    public function setmime_type($mime_type) {
        $this->mime_type = $mime_type;

        return $this;
    }

    /**
     * Get mime_type
     *
     * @return string
     */
    public function getmime_type() {
        return $this->mime_type;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return V3s3
     */
    public function setstatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getstatus() {
        return $this->status;
    }

    /**
     * Set timestamp_deleted
     *
     * @param integer $timestamp_deleted
     *
     * @return V3s3
     */
    public function settimestamp_deleted($timestamp_deleted) {
        $this->timestamp_deleted = $timestamp_deleted;

        return $this;
    }

    /**
     * Get timestamp_deleted
     *
     * @return integer
     */
    public function gettimestamp_deleted() {
        return $this->timestamp_deleted;
    }

    /**
     * Set date_time_deleted
     *
     * @param string $date_time_deleted
     *
     * @return V3s3
     */
    public function setdate_time_deleted($date_time_deleted) {
        $this->date_time_deleted = $date_time_deleted;

        return $this;
    }

    /**
     * Get date_time_deleted
     *
     * @return string
     */
    public function getdate_time_deleted() {
        return $this->date_time_deleted;
    }

    /**
     * Set ip_deleted_from
     *
     * @param string $ip_deleted_from
     *
     * @return V3s3
     */
    public function setip_deleted_from($ip_deleted_from) {
        $this->ip_deleted_from = $ip_deleted_from;

        return $this;
    }

    /**
     * Get ip_deleted_from
     *
     * @return string
     */
    public function getip_deleted_from() {
        return $this->ip_deleted_from;
    }

	public function isDeleted() {
		return ((int)$this->getstatus() === self::STATUS_DELETED);
	}
}
