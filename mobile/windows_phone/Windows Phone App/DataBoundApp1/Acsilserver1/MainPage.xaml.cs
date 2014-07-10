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

namespace Acsilserver1
{
    public partial class MainPage : PhoneApplicationPage
    
    {
        private string url;

        private int AUTH = 0;
        private int TOKEN = 1;
        private int TOKEN2 = 2;
        private int count = 0;
        private int state;
        private string code;


        // Constructeur
        public MainPage()
        {
            InitializeComponent();          
            state = AUTH;
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
        void GetRequestStreamCallback(IAsyncResult callbackResult)
        {
            if (state == AUTH)
            {
                HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
                // End the stream request operation
                Stream postStream = myRequest.EndGetRequestStream(callbackResult);
 
                // Create the post data
                string postData = "consumer_key=cosumerKey&redirect_uri=http://www.google.com";
                byte[] byteArray = Encoding.UTF8.GetBytes(postData);
 
                // Add the post data to the web request
                postStream.Write(byteArray, 0, byteArray.Length);
                postStream.Close();
 
                // Start the web request
                myRequest.BeginGetResponse(new AsyncCallback(GetResponsetStreamCallback), myRequest);
            }
           else if (state == TOKEN)
            {
                HttpWebRequest myRequest = (HttpWebRequest)callbackResult.AsyncState;
                // End the stream request operation
                Stream postStream = myRequest.EndGetRequestStream(callbackResult);
 
                // Create the post data
                string postData = "consumer_key=consumerKey="+code;
                byte[] byteArray = Encoding.UTF8.GetBytes(postData);
 
                // Add the post data to the web request
                postStream.Write(byteArray, 0, byteArray.Length);
                postStream.Close();
 
                // Start the web request
                myRequest.BeginGetResponse(new AsyncCallback(GetResponsetStreamCallback), myRequest);
            }
        }
        void GetResponsetStreamCallback(IAsyncResult callbackResult)
        {
            if (state == AUTH)
            {
                HttpWebRequest request = (HttpWebRequest)callbackResult.AsyncState;
                HttpWebResponse response = (HttpWebResponse)request.EndGetResponse(callbackResult);
                if (response.StatusCode == System.Net.HttpStatusCode.OK)
                {
                    Debug.WriteLine("Ok");
                }
                else
                {
                    Debug.WriteLine(response.StatusCode);
                }
                using (StreamReader httpWebStreamReader = new StreamReader(response.GetResponseStream()))
                {
                    string result = httpWebStreamReader.ReadToEnd();
                    //For debug: show results
                    Debug.WriteLine(result);
                    string[] data = result.Split('=');
                    url = "https://getpocket.com/auth/authorize?request_token=" + data[1] + "&redirect_uri=http://www.google.com";
                    code = data[1];
 
                    Deployment.Current.Dispatcher.BeginInvoke(() =>
                    {
                        // change UI here
                        web1.Visibility = System.Windows.Visibility.Visible;
                        web1.Navigate(new Uri(url));
                    });
                }
            }
            else if (state == TOKEN)
            {
                HttpWebRequest request = (HttpWebRequest)callbackResult.AsyncState;
                HttpWebResponse response = (HttpWebResponse)request.EndGetResponse(callbackResult);
                if (response.StatusCode == System.Net.HttpStatusCode.OK)
                {
                    Debug.WriteLine("Ok1");
                }
                else
                {
                    Debug.WriteLine(response.StatusCode);
                }
                using (StreamReader httpWebStreamReader = new StreamReader(response.GetResponseStream()))
                {
                    string result = httpWebStreamReader.ReadToEnd();
                    //For debug: show results
                    Debug.WriteLine(result);
 
                }
            }
        }
 
        private void Button_Click_1(object sender, RoutedEventArgs e)
        {
            System.Uri myUri = new System.Uri("https://getpocket.com/v3/oauth/request");
            HttpWebRequest myRequest = (HttpWebRequest)HttpWebRequest.Create(myUri);
            myRequest.Method = "POST";
            myRequest.ContentType = "application/x-www-form-urlencoded";
            myRequest.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), myRequest);
 
        }
 
        private void Button_Click_2(object sender, RoutedEventArgs e)
        {
            web1.Navigate(new Uri(url));
 
        }
 
        private void web1_Navigated(object sender, NavigationEventArgs e)
        {
 
 
            if (strCmp(e.Uri.AbsoluteUri,"http://www.google.") == true)
            {
                web1.Visibility = System.Windows.Visibility.Collapsed;
                MessageBox.Show("complete");
                HttpWebRequest myRequest = (HttpWebRequest)HttpWebRequest.Create("https://getpocket.com/v3/oauth/authorize");
                myRequest.Method = "POST";
                myRequest.ContentType = "application/x-www-form-urlencoded";
                myRequest.BeginGetRequestStream(new AsyncCallback(GetRequestStreamCallback), myRequest);
                state = TOKEN;
            }
 
        }
        private bool strCmp(string a, string b)
        {
            if(a.Length < b.Length)
                return false;
            bool equal = false;
            for (int i = 0; i < b.Length; i++)
            {
 
                if (a[i] == b[i])
                    equal = true;
                else
                {
                    equal = false;
                    break;
                }
 
            }
 
            return equal;
        }


        private void Button_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (!string.IsNullOrEmpty(Login.Text) && !string.IsNullOrEmpty(URL.Text) && !string.IsNullOrEmpty(Password.Password))
            {
                //tenter connection
                HttpWebRequest requete = (HttpWebRequest)HttpWebRequest.Create("http://localhost:8081/web/app_dev.php/service/1/op/list");
                requete.Method = "POST";
                requete.ContentType = "application/json";
       
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
                    NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml", UriKind.RelativeOrAbsolute));
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