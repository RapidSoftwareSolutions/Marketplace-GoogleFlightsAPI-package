[![](https://scdn.rapidapi.com/RapidAPI_banner.png)](https://rapidapi.com/package/GoogleFlightsAPI/functions?utm_source=RapidAPIGitHub_GoogleFlightsFunctions&utm_medium=button&utm_content=RapidAPI_GitHub)

# GoogleFlightsAPI Package
Return real-time flight prices and availabilities
* Domain: google.com
* Credentials: apiKey

## How to get credentials: 
0. Go to [Google Developers Console](https://console.developers.google.com/?authuser=1);
1. Select a project, or create a new one.
2. Press **Continue** to activate API key.
3. In the sidebar on the left, select **Credentials**.
4. If your project has no API key for the server, create it now - **Add credentials > API key > Server key**;

## Custom datatypes: 
 |Datatype|Description|Example
 |--------|-----------|----------
 |Datepicker|String which includes date and time|```2016-05-28 00:00:00```
 |Map|String which includes latitude and longitude coma separated|```50.37, 26.56```
 |List|Simple array|```["123", "sample"]``` 
 |Select|String with predefined values|```sample```
 |Array|Array of objects|```[{"Second name":"123","Age":"12","Photo":"sdf","Draft":"sdfsdf"},{"name":"adi","Second name":"bla","Age":"4","Photo":"asfserwe","Draft":"sdfsdf"}] ```
 

## GoogleFlightsAPI.searchSingleTrip
This endpoint returns information about single trip.

| Field                      | Type       | Description
|----------------------------|------------|----------
| apiKey                     | credentials| Required: Your ApiKey obtained from Google Developer Console.
| origin                     | String     | Required: The departure city. 3-digit code IATA format, for example: BOS - Boston.
| destination                | String     | Required: The city of arrival. 3-digit code IATA format, for example: BOS - Boston.
| passengersAdultCount       | String     | Required: The number of passengers that are adults.
| passengersChildCount       | String     | Required: The number of passengers that are children.
| fromDate                   | Datepicker     | Required: The date of departure. Format:  YYYY-mm-dd. Example: 2016-12-20
| toDate                     | Datepicker     | Optional: The return flight date. Format:  YYYY-mm-dd. Example: 2016-12-20
| maxPrice                   | String     | Optional: Do not return solutions that cost more than this price. The currency is specified in ISO-4217. The format, in regex, is [A-Z] {3} \d+(\.\d+)?
| refundable                 | String     | Optional: Return only solutions with refundable fares.
| solutions                  | String     | Optional: The number of solutions to return, maximum 500.

## GoogleFlightsAPI.searchTrips
This endpoint returns a list of flights.

| Field                      | Type       | Description
|----------------------------|------------|----------
| apiKey                     | credentials| Required: Your ApiKey obtained from Google Developer Console.
| fields                     | String     | Optional: Selector specifying a subset of fields to include in the response. For more information, see the partial response documentation. Use for better performance..
| quotaUser                  | String     | Optional: Alternative to userIp. Lets you enforce per-user quotas from a server-side application even in cases when the user's IP address is unknown. This can occur, for example, with applications that run cron jobs on App Engine on a user's behalf. You can choose any arbitrary string that uniquely identifies a user, but it is limited to 40 characters. Overrides userIp if both are provided. Learn more about capping usage.
| userIp                     | String     | Optional: IP address of the end user for whom the API call is being made. Lets you enforce per-user quotas when calling the API from a server-side application. Learn more about capping usage.
| passengersKind             | String     | Required: Identifies this as a passenger count object, representing the number of passengers. Value: the fixed string qpxexpress#passengerCounts.
| passengersAdultCount       | String     | Required: The number of passengers that are adults.
| passengersChildCount       | String     | Required: The number of passengers that are children.
| passengersInfantInLapCount | String     | Required: The number of passengers that are infants travelling in the lap of an adult.
| passengersInfantInSeatCount| String     | Required: The number of passengers that are infants each assigned a seat.
| passengersSeniorCount      | String     | Required: The number of passengers that are senior citizens.
| slices                     | List       | Required: Array of json objects. The slices that make up the itinerary of this trip. A slice represents a traveler's intent, the portion of a low-fare search corresponding to a traveler's request to get between two points. One-way journeys are generally expressed using one slice, round-trips using two. An example of a two slice trip would be: BOS-LAX 10MAR2017, LAX-BOS 22MAR2017. Example: [{"kind": "qpxexpress#sliceInput","origin": "BOS","destination": "LAX","date": "2017-03-10"},{"kind": "qpxexpress#sliceInput","origin": "LAX","destination": "BOS","date": "2017-10-22"}]
| maxPrice                   | String     | Optional: Do not return solutions that cost more than this price. The currency is specified in ISO-4217. The format, in regex, is [A-Z] {3} \d+(\.\d+)?
| saleCountry                | String     | Required: IATA country code representing the point of sale. This determines the "equivalent amount paid" currency for the ticket.
| ticketingCountry           | String     | Required: IATA country code representing the point of ticketing.
| refundable                 | String     | Optional: Return only solutions with refundable fares.
| solutions                  | String     | Optional: The number of solutions to return, maximum 500.

#### slices format
```json
[  
    {  
        "kind":"qpxexpress#sliceInput",
        "origin":"BOS",
        "destination":"LAX",
        "date":"2017-03-10"
    },
    {  
        "kind":"qpxexpress#sliceInput",
        "origin":"LAX",
        "destination":"BOS",
        "date":"2017-10-22"
    }
]
```

