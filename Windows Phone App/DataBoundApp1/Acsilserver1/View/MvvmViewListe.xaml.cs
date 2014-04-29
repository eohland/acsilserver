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
using Acsilserver1.ViewModels;

namespace Acsilserver1
{
    public partial class MvvmView1 : PhoneApplicationPage
    {
        private string folderID = "";
        // Constructor
        public MvvmView1()
        {
            InitializeComponent();

            // Sample code to localize the ApplicationBar
            //BuildLocalizedApplicationBar();

            DataContext = App.ViewModel;

            // Exemple de code pour la localisation d'ApplicationBar
            //BuildLocalizedApplicationBar();
        }

        // Charger les données pour les éléments ViewModel
        protected override void OnNavigatedTo(NavigationEventArgs e)
        {
            string folder = "";
            if (NavigationContext.QueryString.TryGetValue("folder", out folder))
            {
                folderID = folder;
            }
            else
            {
                folderID = "0";
            }

            //TODO -> si le click vient de la list et que c'est pour un dossier -> faire le load
            
            string g = e.Uri.ToString();
            if (!App.ViewModel.IsDataLoaded)
                App.ViewModel.LoadData(folderID);
        }

        // Gérer la sélection modifiée sur LongListSelector
        private void MainLongListSelector_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            // Si l'élément sélectionné a la valeur Null (pas de sélection), ne rien faire
            if (MainLongListSelector.SelectedItem == null)
                return;

            // Naviguer vers la nouvelle page
            if ((MainLongListSelector.SelectedItem as ItemViewModel).Type == "/Assets/Icon/folder.png")
                NavigationService.Navigate(new Uri("/View/MvvmViewListe.xaml?folder=" + (MainLongListSelector.SelectedItem as ItemViewModel).URL, UriKind.Relative));
            else
                NavigationService.Navigate(new Uri("/View/MvvmViewFile.xaml?selectedItem=" + (MainLongListSelector.SelectedItem as ItemViewModel).ID, UriKind.Relative));

            // Réinitialiser l'élément sélectionné sur Null (pas de sélection)
            MainLongListSelector.SelectedItem = null;
        }

        // Sample code for building a localized ApplicationBar
        private void BuildLocalizedApplicationBar()
        {
            // Set the page's ApplicationBar to a new instance of ApplicationBar.
            ApplicationBar = new ApplicationBar();

           // Create a new button and set the text value to the localized string from AppResources.
            ApplicationBarIconButton appBarButton = new ApplicationBarIconButton(new Uri("/Assets/AppBar/appbar.add.rest.png", UriKind.Relative));
            appBarButton.Text = AppResources.AppBarButtonRefresh;
    
            ApplicationBar.Buttons.Add(appBarButton);

            // Create a new menu item with the localized string from AppResources.
            ApplicationBarMenuItem appBarMenuItem = new ApplicationBarMenuItem(AppResources.AppBarMenuItemText);
            ApplicationBar.MenuItems.Add(appBarMenuItem);
        }

        private void AppBarRefresh_Click(object sender, EventArgs e)
        {

            App.ViewModel.LoadData(folderID);
            
        }
    }
}