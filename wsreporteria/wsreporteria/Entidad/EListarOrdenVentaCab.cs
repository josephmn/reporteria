﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace wsreporteria.Entity
{
    public class EListarOrdenVentaCab
    {
        public String ORDEN_VENTA { get; set; }
        public String FECHA_ORDEN { get; set; }
        public String PERIODO { get; set; }
        public String ALMACEN { get; set; }
        public String RUC { get; set; }
        public String RAZON { get; set; }
        public Double MONTO_ORDEN { get; set; }
        public String PORCEN_PENDIENTE { get; set; }
        public String PORCEN_ATENCION { get; set; }
    }
}