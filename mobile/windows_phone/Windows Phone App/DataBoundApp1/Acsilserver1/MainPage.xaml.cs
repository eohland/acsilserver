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

namespace Acsilserver1
{
    public partial class MainPage : PhoneApplicationPage
    
    {

        string grantType = "password";
        string applicationID = "1_1czy7ecwsklcw84c8woococ4cg0ko44cwoosgkgw8w0kcck448";
        string clientString = "2k4nxulmjk2swsws00ooosswoo40ko0sok04c8kss4sk4woo0g";
        string username = null;
        string password = null;
        string APIurl = "app_dev.php/oauth/v2/token";
        HttpWebResponse response = null;

        // Constructeur
        public MainPage()
        {
            InitializeComponent();          
            // Exemple de code pour la localisation d'ApplicationBar
            //BuildLocalizedApplicationBar();
        }


        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {
            bool isLogged = false;

            if (IsolatedStorageSettings.ApplicationSettings.Contains("logged"))
                isLogged = (bool)IsolatedStorageSettings.ApplicationSettings["logged"];

            if (IsolatedStorageSettings.ApplicationSettings.Contains("Login"))
                Login.Text = (string)IsolatedStorageSettings.ApplicationSettings["Login"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("URL"))
                URL.Text = (string)IsolatedStorageSettings.ApplicationSettings["URL"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("Pwd"))
                Password.Password = (string)IsolatedStorageSettings.ApplicationSettings["Pwd"];
            if (IsolatedStorageSettings.ApplicationSettings.Contains("Save"))
                Save.IsChecked = (bool)IsolatedStorageSettings.ApplicationSettings["Save"];

            if (isLogged == true)
            {
                object sender = new object();
                System.Windows.Input.GestureEventArgs ert = new System.Windows.Input.GestureEventArgs();
                Button_Tap(sender, ert);
            }

            base.OnNavigatedTo(e);
        }
        // Exemple de code pour la conception d'une ApplicationBar localisée
        //private void BuildLocalizedApplicationBar()
        //{
        //    // Définit l'ApplicationBar de la page sur une nouvelle instance d'ApplicationBar.
        //    ApplicationBar = new ApplicationBar();

        //    // Crée un bouton et définit la valeur du texte sur la chaîne localisée issue d'AppResources.
        //    ApplicationBarIconButton appBarButton = new ApplicationBarIconButton(new Uri("/Assets/AppBar/appbar.add.rest.png", UriKind.Relative));
        //    appBarButton.Text = AppResources.AppBarButtonText;
        //    ApplicationBar.Buttons.Add(appBarButton);

        //    // Crée un nouvel élément de menu avec la chaîne localisée d'AppResources.
        //    ApplicationBarMenuItem appBarMenuItem = new ApplicationBarMenuItem(AppResources.AppBarMenuItemText);
        //    ApplicationBar.MenuItems.Add(appBarMenuItem);
        //}

        private void GetRequestStreamCallback(IAsyncResult callbackResult)
        {
            HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
            Stream postStream = myRequest.EndGetRequestStream(callbackResult);
            StringBuilder data = new StringBuilder();
            data.Append("grant_type=" + Uri.EscapeDataString(grantType));
            data.Append("&client_id=" + Uri.EscapeDataString(applicationID));
            data.Append("&client_secret=" + Uri.EscapeDataString(clientString));
            data.Append("&username=" + Uri.EscapeDataString(username));
            data.Append("&password=" + Uri.EscapeDataString(password));
            byte[] byteArray = Encoding.UTF8.GetBytes(data.ToString());
            postStream.Write(byteArray, 0, byteArray.Length);
            postStream.Close();
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

                string rssTitle = (string)o["access_token"];

                PhoneApplicationService.Current.State["token"] = rssTitle;
                IsolatedStorageSettings.ApplicationSettings["token"] = rssTitle;
                    Dispatcher.BeginInvoke(
                    (Action)(() =>
                        {
                            NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml", UriKind.RelativeOrAbsolute));
                        }));

            }
            catch (Exception e)
            {
                Dispatcher.BeginInvoke(
                    (Action)(() =>
                    {
                        MessageBox.Show("Mauvaise identification");
                    }));
            }
        }

        private void Button_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (!string.IsNullOrEmpty(Login.Text) && !string.IsNullOrEmpty(URL.Text) && !string.IsNullOrEmpty(Password.Password))
            {
                string destinationURL = URL.Text + APIurl;
                username = Login.Text;
                password = Password.Password;
                HttpWebRequest spAuthReq = HttpWebRequest.Create(destinationURL) as HttpWebRequest;
                spAuthReq.ContentType = "application/x-www-form-urlencoded";
                spAuthReq.Method = "POST";
                spAuthReq.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), spAuthReq);

                PhoneApplicationService.Current.State["URL"] = URL.Text;
                PhoneApplicationService.Current.State["login"] = Login.Text;
                PhoneApplicationService.Current.State["Pwd"] = Password.Password;
                PhoneApplicationService.Current.State["Save"] = Save.IsChecked.ToString();
                //le token
                
                if (Save.IsChecked == true)
                {
                    IsolatedStorageSettings.ApplicationSettings["logged"] = true;
                    IsolatedStorageSettings.ApplicationSettings["Login"] = Login.Text;
                    IsolatedStorageSettings.ApplicationSettings["URL"] = URL.Text;
                    IsolatedStorageSettings.ApplicationSettings["Pwd"] = Password.Password;
                    IsolatedStorageSettings.ApplicationSettings["Save"] = true;
                }
                else
                {
                    IsolatedStorageSettings.ApplicationSettings["Save"] = false; 
                }
                try
                {
                    //NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml", UriKind.RelativeOrAbsolute));
                }
                catch (Exception exc)
                {
                    string toto = exc.Message;
                }
            }
            else
            {
                if (string.IsNullOrEmpty(Login.Text) && string.IsNullOrEmpty(URL.Text) && string.IsNullOrEmpty(Password.Password))
                {
                    Erreur.Text = "Veuillez remplir tous les champs.";
                }
                    // Gerer les codes de retours de l'API
                else
                {
                    Erreur.Text = "Mauvais mot de passe ou identifiants";
                }
                Erreur.Visibility = System.Windows.Visibility.Visible;
            }
        }

    }
}