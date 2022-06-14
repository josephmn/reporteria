using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace wsreporteria.Entity
{
    public class EListarEmbarquesXsku
    {
        public String ORDEN_VENTA { get; set; }
        public Int32 FILA { get; set; }
        public String SKU { get; set; }
        public Double PRECIO { get; set; }
        public Double CANTIDAD_ORDEN { get; set; }
        public Double CANTIDAD_LLEVA { get; set; }
    }
}