<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmecefInvoices extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $fillable = ['code_mecef', 'nim_mecef', 'compteurs_mecef', 'qrcode_mecef', 'date_mecef', 'transaction_id'];


}
