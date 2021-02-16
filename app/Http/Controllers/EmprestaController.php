<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

class EmprestaController extends Controller
{
    //

    public function instituicoes() {
        $json = '../storage/app/simulador/instituicoes.json';
        $content = json_decode(file_get_contents($json), true);
        return $content;        
    }

    public function convenios(){
        $json = '../storage/app/simulador/convenios.json';
        $content = json_decode(file_get_contents($json), true);
        return $content;        
    }

    public function simulador(Request $request)

    {
        $data = $request->all();
        
        $validacao = Validator::make($data, [
            'valor_emprestimo' => ['required','regex:/^\d+(\.\d{1,2})?$/'],
            'instituicao' => ['nullable','array'],
            'convenios' => ['nullable','array'],
            'parcela' => ['nullable','numeric'],
        ]); 
        
        if($validacao->fails()){
            return ['status'=>false, "validacao"=>true, "erros"=>$validacao->errors()];
        }

        $json = '../storage/app/simulador/taxas_instituicoes.json';
        $content = json_decode(file_get_contents($json), true);

        
        if ($data['instituicao'] == "") {
            //
        } else {

            $inst = $data['instituicao'];
            //$r = "";
            foreach ($inst as $ns) {
                echo $ns . ": [\n";

                foreach($content as $item){ 
                    if ($item['instituicao'] == $ns) {

                        $vp = $data['valor_emprestimo'] * $item['coeficiente'];
                        
                        $r = array(
                            'taxa' => $item['taxaJuros'],
                            'parcelas' => $item['parcelas'],
                            'valor_parcela' => number_format($vp, 2, '.', ' '),
                            'convenio' => $item['convenio']
                        );
                        $m1 = json_encode($r, true);
                        echo $m1;                        
                        
                    }
                }
                echo "\n]\n";

            }

        }

    }
}
