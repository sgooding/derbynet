package org.jeffpiazza.derby.devices;

import java.util.logging.Level;
import java.util.logging.Logger;
import jssc.SerialPortException;
import org.jeffpiazza.derby.Message;
import org.jeffpiazza.derby.SerialPortWrapper;

public abstract class TimerDeviceBase implements TimerDevice {
  protected SerialPortWrapper portWrapper;

  private RaceStartedCallback raceStartedCallback;
  private RaceFinishedCallback raceFinishedCallback;
  private StartingGateCallback startingGateCallback;
  private TimerMalfunctionCallback timerMalfunctionCallback;

  protected TimerDeviceBase(SerialPortWrapper portWrapper) {
    this.portWrapper = portWrapper;
  }

  public SerialPortWrapper getPortWrapper() {
    return portWrapper;
  }

  // Returns true if we've had any response recently, otherwise invokes
  // malfunction callback and returns false.
  protected void checkConnection() throws LostConnectionException {
    if (portWrapper.millisSinceLastContact() > 2000) {
      throw new LostConnectionException();
    }
  }

  public synchronized void registerRaceStartedCallback(
      RaceStartedCallback raceStartedCallback) {
    this.raceStartedCallback = raceStartedCallback;
  }

  protected synchronized RaceStartedCallback getRaceStartedCallback() {
    return raceStartedCallback;
  }

  protected void invokeRaceStartedCallback() {
    RaceStartedCallback cb = getRaceStartedCallback();
    if (cb != null) {
      cb.raceStarted();
    }
  }

  public synchronized void registerRaceFinishedCallback(
      RaceFinishedCallback raceFinishedCallback) {
    this.raceFinishedCallback = raceFinishedCallback;
  }

  protected synchronized RaceFinishedCallback getRaceFinishedCallback() {
    return raceFinishedCallback;
  }

  protected void invokeRaceFinishedCallback(Message.LaneResult[] results) {
    RaceFinishedCallback cb = getRaceFinishedCallback();
    if (cb != null) {
      cb.raceFinished(results);
    }
  }

  public synchronized void registerStartingGateCallback(
      StartingGateCallback startingGateCallback) {
    this.startingGateCallback = startingGateCallback;
  }

  protected synchronized StartingGateCallback getStartingGateCallback() {
    return startingGateCallback;
  }

  protected void invokeGateChangeCallback(boolean isOpen) {
    StartingGateCallback cb = getStartingGateCallback();
    if (cb != null) {
      cb.startGateChange(isOpen);
    }
  }

  public synchronized void registerTimerMalfunctionCallback(
      TimerMalfunctionCallback timerMalfunctionCallback) {
    this.timerMalfunctionCallback = timerMalfunctionCallback;
  }

  protected synchronized TimerMalfunctionCallback getTimerMalfunctionCallback() {
    return timerMalfunctionCallback;
  }

  @Override
  public synchronized void invokeMalfunctionCallback(boolean detectable,
                                                     String msg) {
    TimerMalfunctionCallback cb = getTimerMalfunctionCallback();
    if (cb != null) {
      cb.malfunction(detectable, msg);
    }
  }

  @Override
  public void close() {
    try {
      portWrapper.port().closePort();
    } catch (SerialPortException ex) {
      Logger.getLogger(TimerDeviceBase.class.getName()).log(Level.SEVERE, null,
                                                            ex);
    }
  }
}