using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Services;
using wsreporteria.Entity;
using wsreporteria.view;

namespace wsreporteria
{
    /// <summary>
    /// Descripción breve de wsreporteria
    /// </summary>
    [WebService(Namespace = "http://verdum.com/")]
    [WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
    [System.ComponentModel.ToolboxItem(false)]
    // Para permitir que se llame a este servicio web desde un script, usando ASP.NET AJAX, quite la marca de comentario de la línea siguiente. 
    // [System.Web.Script.Services.ScriptService]
    public class wsreporteria : System.Web.Services.WebService
    {

        public VListarOrdenVentaCab obEListarOrdenVentaCab = new VListarOrdenVentaCab();
        public VListarOrdenVentaDet obEListarOrdenVentaDet = new VListarOrdenVentaDet();
        public VListarEmbarquesXorden obEListarEmbarquesXorden = new VListarEmbarquesXorden();
        public VListarEmbarquesXsku obEListarEmbarquesXsku = new VListarEmbarquesXsku();

        public VListarFillRateXsku obEListarFillRateXsku = new VListarFillRateXsku();
        public VListarFVDXsku obEListarFVDXsku = new VListarFVDXsku();

        [WebMethod]
        public string ListarOrdenVentaCab(Int32 post)
        {
            List<EListarOrdenVentaCab> lista = new List<EListarOrdenVentaCab>();
            lista = obEListarOrdenVentaCab.ListarOrdenVentaCab(post);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }

        [WebMethod]
        public string ListarOrdenVentaDet(String orden)
        {
            List<EListarOrdenVentaDet> lista = new List<EListarOrdenVentaDet>();
            lista = obEListarOrdenVentaDet.ListarOrdenVentaDet(orden);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }

        [WebMethod]
        public string ListarEmbarquesXorden(String orden)
        {
            List<EListarEmbarquesXorden> lista = new List<EListarEmbarquesXorden>();
            lista = obEListarEmbarquesXorden.ListarEmbarquesXorden(orden);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }

        [WebMethod]
        public string ListarEmbarquesXsku(String orden, String embarque, String sku)
        {
            List<EListarEmbarquesXsku> lista = new List<EListarEmbarquesXsku>();
            lista = obEListarEmbarquesXsku.ListarEmbarquesXsku(orden, embarque, sku);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }

        [WebMethod]
        public string ListarFillRateXsku(Int32 post, String almacen)
        {
            List<EListarFillRateXsku> lista = new List<EListarFillRateXsku>();
            lista = obEListarFillRateXsku.ListarFillRateXsku(post, almacen);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }

        [WebMethod]
        public string ListarFVDXsku(Int32 post, String almacen)
        {
            List<EListarFVDXsku> lista = new List<EListarFVDXsku>();
            lista = obEListarFVDXsku.ListarFVDXsku(post, almacen);
            string json = JsonConvert.SerializeObject(lista);
            return json;
        }
    }
}
