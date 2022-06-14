using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using wsreporteria.Controller;
using wsreporteria.Entity;

namespace wsreporteria.view
{
    public class VListarOrdenVentaCab : BDconexion
    {
        public List<EListarOrdenVentaCab> ListarOrdenVentaCab()
        {
            List<EListarOrdenVentaCab> lCListarOrdenVentaCab = null;
            using (SqlConnection con = new SqlConnection(conexion))
            {
                try
                {
                    con.Open();
                    CListarOrdenVentaCab oVListarOrdenVentaCab = new CListarOrdenVentaCab();
                    lCListarOrdenVentaCab = oVListarOrdenVentaCab.ListarOrdenVentaCab(con);
                }
                catch (SqlException)
                {
                    
                }
            }
                return (lCListarOrdenVentaCab);
        }
    }
}