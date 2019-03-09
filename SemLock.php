<?php
/**
 * A php standalone lock lib implement with semaphore.
 *
 * @author lvshuang <lvshuang1201@gmail.com>
 */
namespace SemLock;

class SemLock
{
    protected $noBlocking  = true;
    protected $autoRelease = false;
    protected $maxAcquire  = 1;
    protected $key;
    protected $id;
    protected $sem;

    const PERM = 0666;

    /**
     * SemLock constructor.
     *
     * @param string $key
     * @param bool   $autoRelease Specifies if the semaphore should be automatically released, not on request shutdown but when the variable you store it's resource ID is freed.
     * @param int    $maxAcquire  The number of processes that can acquire the semaphore simultaneously.
     *
     * @throws InvalidParamsException
     * @throws ErrorException
     */
    public function __construct(string $key, $autoRelease = false, $maxAcquire = 1)
    {
        if (!$key) {
            throw new InvalidParamsException("empty key");
        }
        $this->key         = $key;
        $this->id          = crc32($key);
        $this->maxAcquire  = (int) $maxAcquire;
        $this->autoRelease = (bool) $autoRelease;

        $this->initSem();
    }

    /**
     * Init sem.
     *
     * @throws ErrorException
     */
    protected function initSem()
    {
        $this->sem = $this->sem = sem_get($this->id, $this->maxAcquire, self::PERM, $this->autoRelease);
        if (!$this->sem) {
            throw new ErrorException('get sem failed');
        }
    }

    /**
     * Specifies if the process shouldn't wait for the semaphore to be acquired.
     * If set to true, the call will return false immediately if a semaphore cannot be immediately acquired.
     * Default is true.
     *
     * @param bool $noBlocking
     *
     * @return void
     */
    public function setNoBlocking(bool $noBlocking) : void
    {
        $this->noBlocking = $noBlocking;
    }

    /**
     * Get lock key.
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->getKey();
    }

    /**
     * acquire lock.
     *
     * @return bool
     *
     * @throws ErrorException
     */
    public function acquire() : bool
    {
        if (!is_resource($this->sem)) {
            $this->initSem();
        }
        return sem_acquire($this->sem, $this->noBlocking);
    }

    /**
     * release lock.
     *
     * @return bool
     *
     * @throws ErrorException
     */
    public function release() : bool
    {
        if (!is_resource($this->sem)) {
            throw new ErrorException('sem is not a source');
        }
        return sem_release($this->sem);
    }

    /**
     * Remove the sem; After removing the semaphore, it is no longer accessible.
     *
     * @return bool
     */
    public function remove() : bool
    {
        return sem_remove($this->sem);
    }

}