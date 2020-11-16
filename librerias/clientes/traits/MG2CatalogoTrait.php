<?php

trait MG2CatalogoTrait {

   public function extractUser2BD(\stdClass $u) {
        $bcrypt = new Bcrypt();
        return [
            'first_name' => $u->nombre,
            'last_name' => $u->apellidos,
            'company' => $u->empresa,
            'active' => 1,
            'password' => $bcrypt->hash($u->contrasena)
        ];
   }

   public function extractInfo2BD(\stdClass $info) {
    $almacenPrincipal = (object) $info->almacenes[0];
    $almacenPrincipal->isPrincipal = true;
    $info->almacenes[0] = $almacenPrincipal;
    return [
        'subdivision' => $info->subdivision,
        'almacen' => $almacenPrincipal->id,
        'va_a' => $info->va_a,
        'con_tecdoc' => $info->con_tecdoc,
        'con_matriculas' => (isset($info->con_matricula) ? $info->con_matricula : 0),
        'ver_estadisticas' => $info->ver_estadisticas,
        'ver_facturacion' => $info->ver_facturacion,
        'ver_subdivisiones' => $info->ver_subdivisiones,
        'solo_netos' => $info->solo_netos,
        'solo_pvp' => $info->solo_pvp,
        'es_comercial' => $info->es_comercial,
        'n_representante' => $info->representante ?: 0,
        'n_operador' => $info->operador ?: 0,
        'mensaje_portes' => $info->mensaje_portes,
        'puede_pedir' => $info->puede_pedir,
        've_transportistas' => $info->ve_transportistas,
        'cliente_de_cliente' => (isset($info->id_cliente_de_cliente) ? $info->id_cliente_de_cliente : 0),
        'iban' => ((isset($info->iban)) ? $info->iban : '""'),
        'previo_pago' => ((isset($info->previopago)) ? $info->previopago : '""'),
        // obsoleto?
        // 'cant_almacenes_a_mostrar' => $max_mostrar, 
        'tipos_pedido' => json_encode($info->tipos_pedido),
        'buscadores' => json_encode($info->buscadores),
        'almacenes_permitidos' => json_encode($info->almacenes),
        'redirect_logout' => 1
    ];
   }

}