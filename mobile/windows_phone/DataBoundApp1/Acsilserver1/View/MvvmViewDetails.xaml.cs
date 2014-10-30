using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using Windows.System;
using System.IO;
using System.Text;


namespace Acsilserver1.View
{
    public partial class MvvmViewDetails : PhoneApplicationPage
    {
        private int index;
        private string newname = null;

        // Constructor
        public MvvmViewDetails()
        {
            InitializeComponent();
        }

        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {

            if (DataContext == null)
            {
                string selectedIndex = "";
                if (NavigationContext.QueryString.TryGetValue("selectedItem", out selectedIndex))
                {
                    index = int.Parse(selectedIndex);
                    DataContext = App.ViewModel.Items[index];
                }
            }

            base.OnNavigatedTo(e);
        }

        private void AppBarEdit_Click(object sender, EventArgs e)
        {
            TextBoxName.IsReadOnly = false;
        }

        private void AppBarSave_Click(object sender, EventArgs e)
        {
            App.ViewModel.Items[index].Name = TextBoxName.Text;
            newname = TextBoxName.Text;

            string destinationURL = PhoneApplicationService.Current.State["URL"].ToString() + "app_dev.php/service/1/op/rename";
            HttpWebRequest spAuthReq = HttpWebRequest.Create(destinationURL) as HttpWebRequest;
            spAuthReq.ContentType = "application/x-www-form-urlencoded";
            spAuthReq.Method = "POST";
            spAuthReq.Accept = "application/json, text/plain, */*";
            spAuthReq.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + PhoneApplicationService.Current.State["token"].ToString();


            spAuthReq.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), spAuthReq);
        }

        private void GetRequestStreamCallback(IAsyncResult callbackResult)
        {
            HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
            Stream postStream = myRequest.EndGetRequestStream(callbackResult);
            StringBuilder data = new StringBuilder();
            data.Append("rename[fromId]=" + App.ViewModel.Items[index].ID + "&rename[toName]= " + newname);
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

        private void AppBarDelete_Click(object sender, EventArgs e)
        {


            if (MessageBox.Show("Etes vous sur de vouloir supprimer le fichier?", "Attention", MessageBoxButton.OKCancel) == MessageBoxResult.Cancel)
            {

            }
            else
            {
                string destinationURL = PhoneApplicationService.Current.State["URL"].ToString() + "app_dev.php/service/1/op/delete";
                HttpWebRequest spAuthReq = HttpWebRequest.Create(destinationURL) as HttpWebRequest;
                spAuthReq.ContentType = "application/x-www-form-urlencoded";
                spAuthReq.Method = "POST";
                spAuthReq.Accept = "application/json, text/plain, */*";
                spAuthReq.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + PhoneApplicationService.Current.State["token"].ToString();

                spAuthReq.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallbackDelete), spAuthReq);
            }

        }

        private void GetRequestStreamCallbackDelete(IAsyncResult ar)
        {
            HttpWebRequest myRequest = (HttpWebRequest)ar.AsyncState;
            Stream postStream = myRequest.EndGetRequestStream(ar);
            StringBuilder data = new StringBuilder();
            data.Append("delete[deleteId]=" + App.ViewModel.Items[index].ID);
            string URL = System.Net.HttpUtility.UrlEncode("delete= [deleteId => " + App.ViewModel.Items[index].ID + "]");

            byte[] byteArray = Encoding.UTF8.GetBytes(data.ToString());
            postStream.Write(byteArray, 0, byteArray.Length);
            postStream.Close();

            
            myRequest.BeginGetResponse(new AsyncCallback(GetResponsetStreamCallback2), myRequest);
        }
        private void GetResponsetStreamCallback2(IAsyncResult callbackResult)
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
                    Dispatcher.BeginInvoke(
                    (Action)(() =>
                        {
            NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml", UriKind.Relative));
                        }));
        }


    }
}