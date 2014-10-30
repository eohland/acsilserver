using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using Acsilserver1.Resources;
using System.IO.IsolatedStorage;
using System.IO;
using System.Text;
using System.Diagnostics;
using System.Collections.Specialized;
using System.Net.Http;
using System.Threading.Tasks;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using Acsilserver1.ViewModels;

namespace Acsilserver1
{
    class Class1
    {
        private void AppBarSave_Click(object sender, EventArgs e)
        {
            string destinationURL = "URL de votre acsilserver" + "app_dev.php/service/1/op/rename";
            HttpWebRequest spAuthReq = HttpWebRequest.Create(destinationURL) as HttpWebRequest;
            spAuthReq.ContentType = "application/x-www-form-urlencoded";
            spAuthReq.Method = "POST";
            spAuthReq.Accept = "application/json, text/plain, */*";
            spAuthReq.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + "Token de votre utilisateur";
            spAuthReq.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), spAuthReq);
        }

        private void GetRequestStreamCallback(IAsyncResult callbackResult)
        {
            HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
            Stream postStream = myRequest.EndGetRequestStream(callbackResult);
            StringBuilder data = new StringBuilder();
            data.Append("rename[fromId]=" + "ID du fichier a renomer" + "&rename[toName]= " + "Nouveau nom du fichier");
            byte[] byteArray = Encoding.UTF8.GetBytes(data.ToString());
            postStream.Write(byteArray, 0, byteArray.Length);
            postStream.Close();
            myRequest.BeginGetResponse(new AsyncCallback(GetResponsetStreamCallback), myRequest);
        }

        private void GetResponsetStreamCallback(IAsyncResult callbackResult)
        {
            HttpWebRequest request = (HttpWebRequest)callbackResult.AsyncState;
            HttpWebResponse response = (HttpWebResponse)request.EndGetResponse(callbackResult);
            string responseString = "";
            Stream streamResponse = response.GetResponseStream();
            StreamReader reader = new StreamReader(streamResponse);
            responseString = reader.ReadToEnd();
            streamResponse.Close();
            reader.Close();
            response.Close();
            string result = responseString;
        }
    }
}
