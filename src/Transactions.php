<?php

declare(strict_types = 1);

namespace TronApi;

use Iterator;

final class Transactions implements Iterator {
	private array $transactions;
	private int $position;

	public function __construct(protected Requests $sender,array $transactions){
		$this->transactions = array_filter($transactions,fn(object $transaction) : bool => isset($transaction->success) and $transaction->success === true);
		$this->position = 0;
	}
	public function current() : mixed {
		return $this->transactions[$this->position];
	}
	public function key() : mixed {
		return $this->position;
	}
	public function next() : void {
		$transaction = $this->transactions[$this->position];
		if(isset($transaction->meta->links->next)):
			$transactions = $this->sender->request('GET',$transaction->meta->links->next);
			if(isset($transactions->success) and $transactions->success === true):
				$transactions->iterator = new self($this->sender,array($transactions));
			endif;
			$this->transactions []= $transactions;
		endif;
		$this->position++;
	}
	public function rewind() : void {
		$this->position = 0;
	}
	public function valid() : bool {
		return isset($this->transactions[$this->position]);
	}
}