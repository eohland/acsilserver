using System;
using System.Collections.ObjectModel;
using System.ComponentModel;
using Acsilserver1.Resources;
using System.Net;
using System.IO;
using Microsoft.Phone.Shell;
using System.Text;
using Newtonsoft.Json.Linq;
using System.Windows.Threading;
using System.Windows;

namespace Acsilserver1.ViewModels
{
    public class MainViewModel : INotifyPropertyChanged
    {
        public MainViewModel()
        {
            this.Items = new ObservableCollection<ItemViewModel>();
        }

        /// <summary>
        /// Collection pour les objets ItemViewModel.
        /// </summary>
        public ObservableCollection<ItemViewModel> Items { get; private set; }

        private string _sampleProperty = "Sample Runtime Property Value";
        /// <summary>
        /// Exemple de propriété ViewModel ; cette propriété est utilisée dans la vue pour afficher sa valeur à l'aide d'une liaison
        /// </summary>
        /// <returns></returns>
        public string SampleProperty
        {
            get
            {
                return _sampleProperty;
            }
            set
            {
                if (value != _sampleProperty)
                {
                    _sampleProperty = value;
                    NotifyPropertyChanged("SampleProperty");
                }
            }
        }

        /// <summary>
        /// Exemple de propriété qui retourne une chaîne localisée
        /// </summary>
        public string LocalizedSampleProperty
        {
            get
            {
                return AppResources.SampleProperty;
            }
        }

        public bool IsDataLoaded
        {
            get;
            private set;
        }


        private void GetRequestStreamCallback(IAsyncResult callbackResult)
        {
            HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
            Stream postStream = myRequest.EndGetRequestStream(callbackResult);
            /*StringBuilder data = new StringBuilder();
            //data.Append("Bearer=" + Uri.EscapeDataString(PhoneApplicationService.Current.State["token"].ToString()));        

            byte[] byteArray = Encoding.UTF8.GetBytes(data.ToString());
            postStream.Write(byteArray, 0, byteArray.Length);
            postStream.Close();*/
            myRequest.BeginGetResponse(new AsyncCallback(GetResponsetStreamCallback), myRequest);
        }

        private void GetResponsetStreamCallback(IAsyncResult callbackResult)
        {

            try
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
                JObject o = JObject.Parse(result);


            }
            catch (Exception e)
            {

                string a = e.ToString();
            }
        }

        private void GetResponseCallback(IAsyncResult rez)
        {
            try
            {
                HttpWebRequest hwr = rez.AsyncState as HttpWebRequest;
                hwr.Method = "POST";
                HttpWebResponse response = hwr.EndGetResponse(rez) as HttpWebResponse;
                string a = (new StreamReader(response.GetResponseStream(), Encoding.UTF8)).ReadToEnd();
            }
            catch (Exception e)
            {

                string a = e.ToString();
            }


        }
        /// <summary>
        /// Crée et ajoute quelques objets ItemViewModel dans la collection Items.
        /// </summary>
        public void LoadData(string IdFolder)
        {
            this.IsDataLoaded = false;
            this.Items.Clear();
            // Exemple de données ; remplacer par des données réelles
            /*  this.Items.Add(new ItemViewModel() { ID = "0", LineOne = "runtime one", LineTwo = "Maecenas praesent accumsan bibendum", LineThree = "Facilisi faucibus habitant inceptos interdum lobortis nascetur pharetra placerat pulvinar sagittis senectus sociosqu" });
              this.Items.Add(new ItemViewModel() { ID = "1", LineOne = "runtime two", LineTwo = "Dictumst eleifend facilisi faucibus", LineThree = "Suscipit torquent ultrices vehicula volutpat maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus" });
              this.Items.Add(new ItemViewModel() { ID = "2", LineOne = "runtime three", LineTwo = "Habitant inceptos interdum lobortis", LineThree = "Habitant inceptos interdum lobortis nascetur pharetra placerat pulvinar sagittis senectus sociosqu suscipit torquent" });
              this.Items.Add(new ItemViewModel() { ID = "3", LineOne = "runtime four", LineTwo = "Nascetur pharetra placerat pulvinar", LineThree = "Ultrices vehicula volutpat maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos" });
              this.Items.Add(new ItemViewModel() { ID = "4", LineOne = "runtime five", LineTwo = "Maecenas praesent accumsan bibendum", LineThree = "Maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos interdum lobortis nascetur" });
              this.Items.Add(new ItemViewModel() { ID = "5", LineOne = "runtime six", LineTwo = "Dictumst eleifend facilisi faucibus", LineThree = "Pharetra placerat pulvinar sagittis senectus sociosqu suscipit torquent ultrices vehicula volutpat maecenas praesent" });
              this.Items.Add(new ItemViewModel() { ID = "6", LineOne = "runtime seven", LineTwo = "Habitant inceptos interdum lobortis", LineThree = "Accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos interdum lobortis nascetur pharetra placerat" });
              this.Items.Add(new ItemViewModel() { ID = "7", LineOne = "runtime eight", LineTwo = "Nascetur pharetra placerat pulvinar", LineThree = "Pulvinar sagittis senectus sociosqu suscipit torquent ultrices vehicula volutpat maecenas praesent accumsan bibendum" });
              this.Items.Add(new ItemViewModel() { ID = "8", LineOne = "runtime nine", LineTwo = "Maecenas praesent accumsan bibendum", LineThree = "Facilisi faucibus habitant inceptos interdum lobortis nascetur pharetra placerat pulvinar sagittis senectus sociosqu" });
              this.Items.Add(new ItemViewModel() { ID = "9", LineOne = "runtime ten", LineTwo = "Dictumst eleifend facilisi faucibus", LineThree = "Suscipit torquent ultrices vehicula volutpat maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus" });
              this.Items.Add(new ItemViewModel() { ID = "10", LineOne = "runtime eleven", LineTwo = "Habitant inceptos interdum lobortis", LineThree = "Habitant inceptos interdum lobortis nascetur pharetra placerat pulvinar sagittis senectus sociosqu suscipit torquent" });
              this.Items.Add(new ItemViewModel() { ID = "11", LineOne = "runtime twelve", LineTwo = "Nascetur pharetra placerat pulvinar", LineThree = "Ultrices vehicula volutpat maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos" });
              this.Items.Add(new ItemViewModel() { ID = "12", LineOne = "runtime thirteen", LineTwo = "Maecenas praesent accumsan bibendum", LineThree = "Maecenas praesent accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos interdum lobortis nascetur" });
              this.Items.Add(new ItemViewModel() { ID = "13", LineOne = "runtime fourteen", LineTwo = "Dictumst eleifend facilisi faucibus", LineThree = "Pharetra placerat pulvinar sagittis senectus sociosqu suscipit torquent ultrices vehicula volutpat maecenas praesent" });
              this.Items.Add(new ItemViewModel() { ID = "14", LineOne = "runtime fifteen", LineTwo = "Habitant inceptos interdum lobortis", LineThree = "Accumsan bibendum dictumst eleifend facilisi faucibus habitant inceptos interdum lobortis nascetur pharetra placerat" });
              this.Items.Add(new ItemViewModel() { ID = "15", LineOne = "runtime sixteen", LineTwo = "Nascetur pharetra placerat pulvinar", LineThree = "Pulvinar sagittis senectus sociosqu suscipit torquent ultrices vehicula volutpat maecenas praesent accumsan bibendum" });
              */
            string destinationURL = PhoneApplicationService.Current.State["URL"].ToString() + "app_dev.php/service/1/op/list";
            /*HttpWebRequest spAuthReq = HttpWebRequest.Create(destinationURL) as HttpWebRequest;
            spAuthReq.Method = "POST";
            spAuthReq.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + PhoneApplicationService.Current.State["token"].ToString();
            spAuthReq.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), spAuthReq);*/

            HttpWebRequest hwr = WebRequest.Create(new Uri(destinationURL)) as HttpWebRequest;
            hwr.Method = "POST";
            hwr.Headers[System.Net.HttpRequestHeader.Authorization] = "Bearer " + PhoneApplicationService.Current.State["token"].ToString();

            hwr.BeginGetResponse(GetResponseCallback, hwr);

            /* if (IdFolder == "25")
             {
                 this.Items.Add(new ItemViewModel() { ID = 0, Type = ObtientImage("pictures"), Name = "photo1.jpg", Date = "28/04/2014", URL = "http://www.suchablog.com/wp-content/uploads/2012/07/national-geographic-traveler-photo-contest-2012_01.jpg" });
                 this.Items.Add(new ItemViewModel() { ID = 1, Type = ObtientImage("pictures"), Name = "photo2.jpg", Date = "28/04/2014", URL = "https://lh4.googleusercontent.com/-j0kpqmCa7GE/UTodb7w2VyI/AAAAAAAADbQ/hS01hwvocIE/w1276-h1268/Joconde%2BHD.jpg" });
                
             }
             else
             {
                 this.Items.Add(new ItemViewModel() { ID = 0, Type = ObtientImage("folder"), Name = "folder", Date = "18/04/2014", URL = "25" });

                 this.Items.Add(new ItemViewModel() { ID = 1, Type = ObtientImage("pictures"), Name = "Alcazar.mp3", Date = "18/04/2014", URL = "http://localhost:8080/alcazar.mp3" });
                 this.Items.Add(new ItemViewModel() { ID = 2, Type = "http://acsilserver.com/wp-content/uploads/2013/08/eip.png", Name = "Epitech.jpg", Date = "18/04/2014", URL = "http://acsilserver.com/wp-content/uploads/2013/08/eip.png" });
                 this.Items.Add(new ItemViewModel() { ID = 3, Type = ObtientImage("pictures"), Name = "Titre 01.avi", Date = "18/04/2014", URL = "http://localhost:8080/Titre%2001.avi" });
                 this.Items.Add(new ItemViewModel() { ID = 4, Type = "http://cinedhec.com/wp-content/uploads/2013/02/12603880-drapeau-suede-sur-la-plaie-en-bois-vieux.jpg", Name = "nomdeOuf.jpg", Date = "18/04/2014", URL = "http://cinedhec.com/wp-content/uploads/2013/02/12603880-drapeau-suede-sur-la-plaie-en-bois-vieux.jpg" });
             }*/

            this.IsDataLoaded = true;
        }

        private string ObtientImage(string type)
        {
            if (type == "folder")
                return "/Assets/Icon/folder.png";
            else if (type == "pictures")
                return "/Assets/Icon/camera.png";
            return "/Assets/Icon/erreur.png";
        }

        public event PropertyChangedEventHandler PropertyChanged;
        private void NotifyPropertyChanged(String propertyName)
        {
            PropertyChangedEventHandler handler = PropertyChanged;
            if (null != handler)
            {
                handler(this, new PropertyChangedEventArgs(propertyName));
            }
        }
    }
}