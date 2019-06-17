<?php declare(strict_types=1);

namespace Comquer\Collection;

abstract class Collection extends IterableAndCountable
{
    private $type;

    private $uniqueIndex;

    protected function __construct(array $elements, Type $type = null, UniqueIndex $uniqueIndex = null)
    {
        $this->type = $type;
        $this->uniqueIndex = $uniqueIndex;

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function add($element): self
    {
        if ($this->isTyped()) {
            $this->type->validate($element);
        }

        if ($this->hasUniqueIndex()) {
            $this->uniqueIndex->validate($element, $this->getElements());
        }

        $this->addElement($element);

        return $this;
    }

    public function addMany(self $elements): void
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function get($uniqueIndex)
    {
        if ($this->hasUniqueIndex() === false) {
            throw UniqueIndexException::indexMissing($uniqueIndex);
        }

        foreach ($this->getElements() as $element) {
            if (($this->uniqueIndex)($element) === $uniqueIndex) {
                return $element;
            }
        }

        throw NotFoundException::elementNotFound($uniqueIndex);
    }

    public function filter(callable $filter): self
    {
        $filteredCollection = new static([]);

        foreach ($this as $element) {
            if ($filter($element)) {
                $filteredCollection->add($element);
            }
        }

        return $filteredCollection;
    }

    public function remove($redundantElement): self
    {
        foreach ($this->getElements() as $key => $element) {
            if ($element == $redundantElement) {
                $this->unsetElement($key);
            }
        }

        return $this;
    }

    public function contains($uniqueIndex): bool
    {
        try {
            $this->get($uniqueIndex);
            return true;
        } catch (NotFoundException $exception) {
            return false;
        }
    }

    public function isTyped(): bool
    {
        return $this->type instanceof Type;
    }

    public function hasUniqueIndex(): bool
    {
        return $this->uniqueIndex instanceof UniqueIndex;
    }

    public function isEmpty(): bool
    {
        return empty($this->getElements());
    }
}