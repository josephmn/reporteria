using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Collections.Specialized;
using System.Linq;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using wsreporteria.Entity;

namespace wsreporteria.Controller
{
    public class CListarEmbarquesXorden
    {
        public List<EListarEmbarquesXorden> ListarEmbarquesXorden(SqlConnection con, String orden)
        {
            List<EListarEmbarquesXorden> lEListarEmbarquesXorden = null;
            SqlCommand cmd = new SqlCommand("ASP_LISTAR_EMBARQUES", con);
            cmd.CommandType = CommandType.StoredProcedure;

            cmd.Parameters.AddWithValue("@orden", SqlDbType.VarChar).Value = orden;

            SqlDataReader drd = cmd.ExecuteReader(CommandBehavior.SingleResult);

            if (drd != null)
            {
                lEListarEmbarquesXorden = new List<EListarEmbarquesXorden>();

                EListarEmbarquesXorden obEListarEmbarquesXorden = null;
                while (drd.Read())
                {
                    obEListarEmbarquesXorden = new EListarEmbarquesXorden();
                    obEListarEmbarquesXorden.EMBARQUE = drd["EMBARQUE"].ToString();
                    obEListarEmbarquesXorden.FACTURA = drd["FACTURA"].ToString();
                    obEListarEmbarquesXorden.FECHA_FACTURA = drd["FECHA_FACTURA"].ToString();
                    lEListarEmbarquesXorden.Add(obEListarEmbarquesXorden);
                }
                drd.Close();
            }

            return (lEListarEmbarquesXorden);
        }
    }
}