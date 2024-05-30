<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Commandes;
use Illuminate\Http\Request;
use App\Models\BonDeCommandes;
use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceEmecefController extends Controller
{
    
    private $api_token, $url_facturation;

    public function __construct()
    {
        $this->api_token = config("app.api_gouv_token");
        $this->url_facturation = config("app.api_facturation_url");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $response = Http::withToken($this->api_token)->get($this->url_facturation);

        return $response->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Commandes $commande)
    {
        $bon_de_commandes = BonDeCommandes::where("commande_id", $commande->id)->get();
        
        $items = [];
        $client = [
            "contact" => $commande->customer->numero,
            "ifu" => $commande->customer->ifu,
            "name" => $commande->customer->name,
            "adress" => $commande->customer->adresse
        ];

        foreach ($bon_de_commandes as $bon_de_commande) {

            array_push($items, [
                "name" => Products::find($bon_de_commande->produit_id)->nom,
                "price" => $bon_de_commande->prix_unitaire,
                "quantity" => $bon_de_commande->quantite,
                "taxGroup" => "A"
            ]);

        }

        $datas = [
            "ifu",
            "items" => $items,
            "client" => $client
        ];
        // return $datas;

        $datasValidated = $request->all();
        $response = Http::withToken($this->api_token)->post($this->url_facturation, $datasValidated);
        return $response->json();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $uid
     * @return \Illuminate\Http\Response
     */

    public function endFacturation($uid)
    {

        $response = Http::withToken($this->api_token)->put($this->url_facturation . '/' . $uid . '/confirm');
        $datas = (array) $response->json();

        if(!is_null($response->json("qrCode")))
        {
            $qrcode = QrCode::size(200)->generate($datas['qrCode']);        
            return view("qrcode", compact('datas', 'qrcode'));
        }
        else {
            return $response->json();
        } 

        /**
         * {
           * "dateTime": "14/07/2023 12:11:13",
           * "qrCode": "F;TS01006246;TESTCBVYEIKN7RBNTYUVJ7B5;3202391786505;20230714121113",
           * "codeMECeFDGI": "TEST-CBVY-EIKN-7RBN-TYUV-J7B5",
           * "counters": "4/6 FV",
           * "nim": "TS01006246"
        *}
         */

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
