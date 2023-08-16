<?php

namespace App\Http\Livewire\Admin\Payment;

use App\Models\Payment\UserWalletHistory;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionVerficationComponent extends Component
{
    use WithPagination;
    public function render()
    {
        $transactions = UserWalletHistory::paginate(12);
        //dd($transactions);
        return view('livewire.admin.payment.transaction-verfication-component',['transactions'=>$transactions]);
    }
}
