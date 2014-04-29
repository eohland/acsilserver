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
using System.Windows.Data;
using System.Collections.ObjectModel;
using System.ComponentModel;
 
namespace Acsilserver1.View
{

    public partial class MvvmViewFile : PhoneApplicationPage
    {
        int index = -1;
        // Constructor
        public MvvmViewFile()
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


            Media.Play();

            base.OnNavigatedTo(e);
        }

        private void Media_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (Media.CurrentState == System.Windows.Media.MediaElementState.Playing)
            {
                Media.Pause();
            }
            else
            {
                Media.Play();
            }
        }

        protected override void OnBackKeyPress(System.ComponentModel.CancelEventArgs e)
        {
            Media.Stop();
            
            base.OnBackKeyPress(e);
        }

        private void Button_Click(object sender, RoutedEventArgs e)
        {
            App.ViewModel.Items[index].Name = "toto";
        }
       
    }
}